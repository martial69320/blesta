<?php
/**
 * Service upgrades/downgrades
 *
 * @package blesta
 * @subpackage blesta.app.models
 * @copyright Copyright (c) 2015, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class ServiceChanges extends AppModel
{
    /**
     * Initialize language
     */
    public function __construct()
    {
        parent::__construct();
        Language::loadLang(['service_changes']);

        Loader::loadComponents($this, ['Json']);
    }

    /**
     * Queues a pending service change entry
     *
     * @param $service_id The service ID
     * @param $invoice_id The ID of the invoice
     * @param array $vars An array of information including:
     *
     *  - data An array of input data used later to process the service change
     * @return int The service change ID on success, or void on failure
     */
    public function add($service_id, $invoice_id, array $vars)
    {
        $data = [
            'service_id' => $service_id,
            'invoice_id' => $invoice_id,
            'data' => (isset($vars['data']) ? (array) $vars['data'] : []),
            'status' => 'pending',
            'date_added' => date('c'),
            'date_status' => date('c')
        ];

        $this->Input->setRules($this->getRules($data));

        if ($this->Input->validates($data)) {
            $fields = ['service_id', 'invoice_id', 'status', 'data', 'date_added', 'date_status'];
            $this->Record->insert('service_changes', $data, $fields);

            $change_id = $this->Record->lastInsertId();

            // Log that the service change was created
            $this->logger->info('Created Service Change', array_merge($data, ['id' => $change_id]));

            return $change_id;
        }
    }

    /**
     * Updates a service change entry
     *
     * @param int $service_change_id The ID of the service change to update
     * @param array $vars An array of fields to update:
     *
     *  - status The service change status (one of: 'pending', 'canceled', 'error', 'completed')
     */
    public function edit($service_change_id, array $vars)
    {
        $vars['date_status'] = $this->dateToUtc(date('c'));
        $vars['id'] = $service_change_id;

        $rules = $this->getRules($vars);
        $rules = [
            'id' => [
                'exists' => [
                    'rule' => [[$this, 'validateExists'], 'id', 'service_changes'],
                    'message' => $this->_('ServiceChanges.!error.id.exists', true)
                ]
            ],
            'status' => $rules['status']
        ];
        $this->Input->setRules($rules);

        if ($this->Input->validates($vars)) {
            $fields = ['status', 'date_status'];

            $this->Record->where('id', '=', $service_change_id)->
                update('service_changes', $vars, $fields);

            // Log that the service change was updated
            $log_vars = array_intersect_key($vars, array_flip($fields));
            $this->logger->info('Updated Service Change', array_merge($log_vars, ['id' => $service_change_id]));
        }
    }

    /**
     * Deletes all service changes associated with the given service
     *
     * @param int $service_id The ID of the service whose changes to permanently delete
     */
    public function deleteByService($service_id)
    {
        if (is_numeric($service_id)) {
            $this->Record->from('service_changes')
                ->where('service_id', '=', $service_id)
                ->delete();
        }
    }

    /**
     * Retrieves a service change
     *
     * @param int $service_change_id The ID of the service change to update
     * @return mixed An stdClass object representing the service change, or false otherwise
     */
    public function get($service_change_id)
    {
        $change = $this->Record->select()->from('service_changes')->
            where('id', '=', $service_change_id)->fetch();

        if ($change) {
            $change->data = $this->Json->decode($change->data);
        }
        return $change;
    }

    /**
     * Retrieves a list of all service change entries of the given status
     *
     * @param mixed $status The status of the service changes to fetch, or null for all
     * @param int $service_id The ID of the service the service change belongs to (optional)
     * @return array A list of service change entries
     */
    public function getAll($status = null, $service_id = null)
    {
        $this->Record->select()->from('service_changes');

        // Filter on status
        if ($status) {
            $this->Record->where('status', '=', $status);
        }
        // Filter on service ID
        if ($service_id) {
            $this->Record->where('service_id', '=', $service_id);
        }

        $entries = $this->Record->fetchAll();

        // Decode JSON data
        foreach ($entries as &$entry) {
            $entry->data = $this->Json->decode($entry->data);
        }

        return $entries;
    }

    /**
     * Processes the pending service change by updating the service
     *
     * @param int $service_change_id The ID of the pending service change to update
     */
    public function process($service_change_id)
    {
        if (!isset($this->Services)) {
            Loader::loadModels($this, ['Services']);
        }

        $service_change = $this->get($service_change_id);

        // Process the service change
        if ($service_change && $service_change->status == 'pending') {
            // Format the object to array
            $data = $this->objectToArray($service_change->data);

            // Update the service
            $this->Services->edit($service_change->service_id, $data);

            // Update the queued service change entry
            $errors = $this->Services->errors();
            $status = (empty($errors) ? 'completed' : 'error');
            $this->edit($service_change_id, ['status' => $status]);
        }
    }

    /**
     * Retrieves a presenter representing a set of items, discounts, and taxes
     * for a service change (i.e. upgrade/downgrade)
     *
     * @param int $service_id The ID of the service to change
     * @param array $vars An array of input representing the new service changes:
     *  - configoptions An array of key/value pairs where each key is an
     *      option ID and each value is the selected value
     *  - pricing_id The ID of the new pricing selected
     *  - qty The service quantity
     *  - coupon_code A new coupon code to use
     * @return bool|Blesta\Core\Pricing\Presenter\Type\ServiceChangePresenter The presenter, otherwise false
     */
    public function getPresenter($service_id, array $vars)
    {
        Loader::loadModels($this, ['Companies', 'Coupons', 'Invoices', 'Packages', 'PackageOptions', 'Services']);
        Loader::loadComponents($this, ['SettingsCollection', 'Form']);

        // Service must exist
        if (!($service = $this->Services->get($service_id))) {
            return false;
        }

        // Determine the package and pricing selected to change to
        $pricing_id = (isset($vars['pricing_id']) ? (int) $vars['pricing_id'] : null);
        $pricing = null;
        if ($pricing_id && ($package = $this->Packages->getByPricingId($pricing_id))) {
            foreach ($package->pricing as $price) {
                if ($price->id == $pricing_id) {
                    $pricing = $price;
                    break;
                }
            }
        }

        // Default to the service package and pricing if new pricing is not given
        if (empty($pricing)) {
            $pricing = $service->package_pricing;
            $package = $this->Packages->get($service->package->id);
        }

        // Fetch all new package options
        $formatted_service_options = $this->PackageOptions->formatServiceOptions($service->options);
        $package_options = $this->PackageOptions->getAllByPackageId(
            $package->id,
            $pricing->term,
            $pricing->period,
            $pricing->currency,
            null,
            $formatted_service_options
        );

        // Fetch any coupon that may exist or be set
        $coupons = [];
        if (!empty($vars['coupon_code'])
            && ($coupon = $this->Coupons->getByCode($vars['coupon_code']))
            && $coupon->company_id == Configure::get('Blesta.company_id')
        ) {
            $coupons['new'] = [$coupon];
        }

        // If no coupon was given, fallback to the service coupon, if any
        if (!empty($service->coupon_id)
            && ($coupon = $this->Coupons->get($service->coupon_id))
            && $coupon->company_id == Configure::get('Blesta.company_id')
        ) {
            $coupons['old'] = [$coupon];
            if (empty($coupons['new'])) {
                $coupons['new'] = $coupons['old'];
            }
        }

        // Set options for the builder to use to construct the presenter
        $now = date('c');
        $options = [
            // Include setup fees since we're adding this package term anew
            'includeSetupFees' => true,
            // Line items show they are billed from this date
            'startDate' => $now,
            'prorateStartDate' => $now,
            'prorateEndDate' => (!empty($service->date_renews) ? $service->date_renews . 'Z' : null),
            // Service changes always apply as recurring for non-onetime services
            'recur' => ($pricing->period != 'onetime'),
            'applyDate' => (!empty($service->date_renews) ? $service->date_renews . 'Z' : $now),
            'config_options' => isset($formatted_service_options['configoptions'])
                ? $formatted_service_options['configoptions']
                : [],
            'upgrade' => $package->id != $service->package->id
        ];

        // Retrieve the pricing builder from the container and update the date format options
        $container = Configure::get('container');
        $container['pricing.options'] = [
            'dateFormat' => $this->Companies->getSetting(Configure::get('Blesta.company_id'), 'date_format')
                ->value,
            'dateTimeFormat' => $this->Companies->getSetting(Configure::get('Blesta.company_id'), 'datetime_format')
                ->value
        ];

        $factory = $this->getFromContainer('pricingBuilder');
        $serviceChange = $factory->serviceChange();

        // Build the service change presenter
        $serviceChange->settings($this->SettingsCollection->fetchClientSettings($service->client_id));
        $serviceChange->taxes($this->Invoices->getTaxRules($service->client_id));
        $serviceChange->discounts($coupons);
        $serviceChange->options($options);

        return $serviceChange->build($service, $vars, $package, $pricing, $package_options);
    }

    /**
     * Retrieves a set of items, discounts, and taxes for a service change (i.e. upgrade/downgrade)
     *
     * @deprecated since 4.0.0 - Use ServiceChanges::getPresenter
     *
     * @param int $service_id The ID of the service to change
     * @param array $vars An array of input representing the new service changes:
     *
     *  - configoptions An array of key/value pairs where each key is an
     *      option ID and each value is the selected value
     *  - pricing_id The ID of the new pricing selected
     *  - qty The service quantity
     *  - coupon_code A new coupon code to use
     * @return mixed Boolean false if no valid service is given, otherwise an array of formatted
     *  items, discounts, and taxes from the PricingPresenter:
     *
     *  - items An array of each item removed and added for this change, including
     *      - price The unit price of the item
     *      - qty The quantity of the item
     *      - description The description of the item
     *  - discounts An array of all applying discounts, including
     *      - amount The amount of the discount
     *      - type The type of the discount
     *      - description A description of the discount
     *      - apply An array of indices referencing items to which the discount applies
     *  - taxes An array of arrays of each tax group containing tax rules that apply, including:
     *      - amount The amount of the tax
     *      - type The type of tax
     *      - description The tax description
     *      - apply An array of indices referencing items to which the tax applies
     */
    public function getItems($service_id, array $vars)
    {
        Loader::loadModels($this, ['Coupons', 'Invoices', 'Packages', 'PackageOptions', 'Services']);
        Loader::loadComponents($this, ['SettingsCollection']);

        // Service must exist
        if (!($service = $this->Services->get($service_id))) {
            return false;
        }

        // Fetch client settings and tax rules that may apply
        $settings = $this->SettingsCollection->fetchClientSettings($service->client_id);
        $tax_rules = $this->Invoices->getTaxRules($service->client_id);

        // Determine the package and pricing selected to change to
        $pricing_id = (isset($vars['pricing_id']) ? $vars['pricing_id'] : null);
        $pricing = null;
        if ($pricing_id && ($package = $this->Packages->getByPricingId($pricing_id))) {
            foreach ($package->pricing as $price) {
                if ($price->id == $pricing_id) {
                    $pricing = $price;
                    break;
                }
            }
        }

        // Default to the service package and pricing if new pricing is not given
        if (empty($pricing)) {
            $pricing = $service->package_pricing;
            $package = $this->Packages->get($service->package->id);
        }

        // Set new and old package IDs
        $package_ids = [$service->package->id];
        if (!in_array($package->id, $package_ids)) {
            $package_ids[] = $package->id;
        }

        // Fetch all new package options
        $package_options = $this->PackageOptions->getAllByPackageId(
            $package->id,
            $pricing->term,
            $pricing->period,
            $pricing->currency,
            null,
            $this->PackageOptions->formatServiceOptions($service->options)
        );

        // Fetch any recurring coupon that may exist
        $coupon_id = $service->coupon_id;
        $package_ids = [$service->package->id];
        if (!empty($vars['coupon_code'])
            && ($coupon = $this->Coupons->getForPackages($vars['coupon_code'], null, $package_ids))
        ) {
            $coupon_id = $coupon->id;
        }

        $coupons = [];
        $date_renews = ($service->date_renews ? $service->date_renews . 'Z' : null);
        if ($coupon_id && $date_renews &&
            ($coupon = $this->Coupons->getRecurring($coupon_id, $pricing->currency, $date_renews))) {
            $coupons[] = $coupon;
        }

        // Service changes always apply as recurring for non-onetime services
        $options = ['recur' => ($pricing->period != 'onetime')];
        Loader::loadComponents($this, ['PricingPresenter' => [$settings, $tax_rules, $coupons, $options]]);

        return $this->PricingPresenter->formatServiceChange($service, $vars, $package, $pricing, $package_options);
    }

    /**
     * Retrieves a list of available service change statuses and their language
     *
     * @return array A list of service change statuses and their language
     */
    public function getStatuses()
    {
        return [
            'pending' => $this->_('ServiceChanges.status.pending', true),
            'completed' => $this->_('ServiceChanges.status.completed', true),
            'error' => $this->_('ServiceChanges.status.error', true),
            'canceled' => $this->_('ServiceChanges.status.canceled', true),
        ];
    }

    /**
     * Converts an object and any nested objects into arrays
     *
     * @param stdClass $object An stdClass object
     * @return array An array representing the object and its fields
     */
    private function objectToArray($object)
    {
        $result = [];
        foreach ($object as $key => $value) {
            if (is_object($value)) {
                $value = $this->objectToArray($value);
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Retrieves validation rules for ::add
     *
     * @param array $vars An array of input data for validation
     * @return array The input validation rules
     */
    private function getRules(array $vars)
    {
        return [
            'service_id' => [
                'exists' => [
                    'rule' => [[$this, 'validateExists'], 'id', 'services'],
                    'message' => $this->_('ServiceChanges.!error.service_id.exists')
                ]
            ],
            'invoice_id' => [
                'exists' => [
                    'rule' => [[$this, 'validateExists'], 'id', 'invoices'],
                    'message' => $this->_('ServiceChanges.!error.invoice_id.exists')
                ],
                'unique' => [
                    'rule' => [[$this, 'validateExists'], 'invoice_id', 'service_changes'],
                    'negate' => true,
                    'message' => $this->_('ServiceChanges.!error.invoice_id.unique')
                ]
            ],
            'status' => [
                'valid' => [
                    'rule' => ['in_array', array_keys($this->getStatuses())],
                    'message' => $this->_('ServiceChanges.!error.status.valid')
                ]
            ],
            'data' => [
                'valid' => [
                    'rule' => true,
                    'post_format' => [[$this->Json, 'encode']],
                    'message' => ''
                ]
            ],
            'date_added' => [
                'format' => [
                    'rule' => true,
                    'post_format' => [[$this, 'dateToUtc']],
                    'message' => ''
                ]
            ],
            'date_status' => [
                'format' => [
                    'rule' => true,
                    'post_format' => [[$this, 'dateToUtc']],
                    'message' => ''
                ]
            ]
        ];
    }
}
