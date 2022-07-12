<?php
/**
 * BuycPanel Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.buycpanel
 * @copyright Copyright (c) 2014, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Buycpanel extends Module
{
    /**
     * Initializes the module
     */
    public function __construct()
    {
        // Load the language required by this module
        Language::loadLang('buycpanel', null, dirname(__FILE__) . DS . 'language' . DS);

        // Load config
        $this->loadConfig(dirname(__FILE__) . DS . 'config.json');

        // Load components required by this module
        Loader::loadComponents($this, ['Input']);
    }

    /**
     * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return bool True if the service validates, false otherwise. Sets Input errors when false.
     */
    public function validateService($package, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars));
        return $this->Input->validates($vars);
    }

    /**
     * Attempts to validate an existing service against a set of service info updates. Sets Input errors on failure.
     *
     * @param stdClass $service A stdClass object representing the service to validate for editing
     * @param array $vars An array of user-supplied info to satisfy the request
     * @return bool True if the service update validates or false otherwise. Sets Input errors when false.
     */
    public function validateServiceEdit($service, array $vars = null)
    {
        $this->Input->setRules($this->getServiceRules($vars, true));
        return $this->Input->validates($vars);
    }

    /**
     * Returns the rule set for adding/editing a service
     *
     * @param array $vars A list of input vars
     * @param bool $edit True to get the edit rules, false for the add rules
     * @return array Service rules
     */
    private function getServiceRules(array $vars = null, $edit = false)
    {
        // Set rule to validate IP addresses
        $ip_address_rule = (function_exists('filter_var') ? ['filter_var', FILTER_VALIDATE_IP] : '');
        if (empty($ip_address_rule)) {
            $range = '(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])';
            $ip_address_rule = [['matches', '/^(?:' . $range . "\." . $range . "\." . $range . "\." . $range . ')$/']];
        }

        // Set rules
        $rules = [
            'buycpanel_ipaddress' => [
                'format' => [
                    'rule' => $ip_address_rule,
                    'message' => Language::_('BuycPanel.!error.buycpanel_ipaddress.format', true)
                ]
            ],
            'buycpanel_domain' => [
                'format' => [
                    'rule' => [[$this, 'validateHostName']],
                    'message' => Language::_('BuycPanel.!error.buycpanel_domain.format', true)
                ]
            ]
        ];

        return $rules;
    }

    /**
     * Adds the service to the remote server. Sets Input errors on failure,
     * preventing the service from being added.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being added (if the current service is an addon service
     *  service and parent service has already been provisioned)
     * @param string $status The status of the service being added. These include:
     *  - active
     *  - canceled
     *  - pending
     *  - suspended
     * @param array $options A set of options for the service (optional)
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addService(
        $package,
        array $vars = null,
        $parent_package = null,
        $parent_service = null,
        $status = 'pending',
        $options = []
    ) {
        // Get module row and API
        $module_row = $this->getModuleRow();
        $api = $this->getApi(
            $module_row->meta->email,
            $module_row->meta->key,
            ($module_row->meta->test_mode == 'true')
        );

        // Disallow an ordertype (license) from overriding the package license except
        // when this option is set. See BuycPanel::unsuspendService()
        if (!isset($options['allow_order_type']) || !$options['allow_order_type']) {
            unset($vars['ordertype'], $vars['license_type']);
        }

        // Get fields
        $params = $this->getFieldsFromInput((array)$vars, $package);

        // Set the addon type separately
        $temp_params = $params;
        if (isset($params['addon'])) {
            $temp_params = array_merge($params, $params['addon']);
            unset($temp_params['addon']);
        }

        $this->validateService($package, $vars);

        if ($this->Input->errors()) {
            return;
        }

        // Only provision the service if 'use_module' is true
        if ($vars['use_module'] == 'true') {
            try {
                $command = new BuycpanelAll($api);
                $response = $command->orderIp($temp_params);
                $this->processResponse($api, $response);
            } catch (Exception $e) {
                // Internal Error
                $this->Input->setErrors(['api' => ['internal' => Language::_('BuycPanel.!error.api.internal', true)]]);
            }

            if ($this->Input->errors()) {
                return;
            }
        }

        // Get the first key in the addon array (the license type), or default to the order type (non-addon license)
        $license = $params['ordertype'];
        if (isset($params['addon'])) {
            reset($params['addon']);
            $license = key($params['addon']);
        }

        // Return service fields
        return [
            [
                'key' => 'buycpanel_ipaddress',
                'value' => $params['serverip'],
                'encrypted' => 0
            ],
            [
                'key' => 'buycpanel_domain',
                'value' => $params['domain'],
                'encrypted' => 0
            ],
            [
                'key' => 'buycpanel_license',
                'value' => $license,
                'encrypted' => 0
            ]
        ];
    }

    /**
     * Edits the service on the remote server. Sets Input errors on failure,
     * preventing the service from being edited.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being edited (if the current service is an addon service)
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editService($package, $service, array $vars = null, $parent_package = null, $parent_service = null)
    {
        // Get module row and API
        $module_row = $this->getModuleRow();
        $api = $this->getApi(
            $module_row->meta->email,
            $module_row->meta->key,
            ($module_row->meta->test_mode == 'true')
        );

        // Validate the service-specific fields
        $this->validateServiceEdit($service, $vars);

        if ($this->Input->errors()) {
            return;
        }

        // Force domain to lower case
        $vars['buycpanel_domain'] = strtolower($vars['buycpanel_domain']);

        // Get the service fields
        $service_fields = $this->serviceFieldsToObject($service->fields);

        // Check for fields that changed
        $delta = [];
        foreach ($vars as $key => $value) {
            if (!array_key_exists($key, $service_fields) || $vars[$key] != $service_fields->$key) {
                $delta[$key] = $value;
            }
        }

        // Only provision the service if 'use_module' is true
        if ($vars['use_module'] == 'true') {
            // Only change IP address
            $current_ip = (isset($service_fields->buycpanel_ipaddress) ? $service_fields->buycpanel_ipaddress : '');
            $new_ip = (isset($delta['buycpanel_ipaddress']) ? $delta['buycpanel_ipaddress'] : $current_ip);

            $this->changeIp($api, $current_ip, $new_ip);

            if ($this->Input->errors()) {
                return;
            }
        }

        // Return all the service fields
        $fields = [];
        foreach ($service_fields as $key => $value) {
            $fields[] = ['key' => $key, 'value' => (isset($delta[$key]) ? $delta[$key] : $value), 'encrypted' => 0];
        }

        return $fields;
    }

    /**
     * Cancels the service on the remote server. Sets Input errors on failure,
     * preventing the service from being canceled.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being canceled (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed
     *  array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function cancelService($package, $service, $parent_package = null, $parent_service = null)
    {
        $response = null;

        if (($module_row = $this->getModuleRow())) {
            $module_row = $this->getModuleRow();
            $api = $this->getApi(
                $module_row->meta->email,
                $module_row->meta->key,
                ($module_row->meta->test_mode == 'true')
            );

            // Get the service fields
            $service_fields = $this->serviceFieldsToObject($service->fields);
            $params = [
                'currentip' => (isset($service_fields->buycpanel_ipaddress)
                    ? $service_fields->buycpanel_ipaddress
                    : ''
                ),
                'cancel' => (isset($service_fields->buycpanel_license) ? $service_fields->buycpanel_license : '')
            ];

            try {
                $command = new BuycpanelAll($api);
                $response = $command->cancelIp($params);
                $this->processResponse($api, $response);
            } catch (Exception $e) {
                // Internal Error
                $this->Input->setErrors(['api' => ['internal' => Language::_('BuycPanel.!error.api.internal', true)]]);
            }
        }

        return null;
    }

    /**
     * Suspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being suspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being suspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function suspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        // Suspend the service by cancelling it
        $this->cancelService($package, $service, $parent_package, $parent_service);
    }

    /**
     * Unsuspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being unsuspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being unsuspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function unsuspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        // Get the service fields
        $service_fields = $this->serviceFieldsToObject($service->fields);

        // Re-add the service since suspending the service cancelled it
        $license = (isset($service_fields->buycpanel_license) ? $service_fields->buycpanel_license : '');
        $vars = [
            'use_module' => 'true',
            'buycpanel_domain' => (isset($service_fields->buycpanel_domain) ? $service_fields->buycpanel_domain : ''),
            'buycpanel_ipaddress' => (isset($service_fields->buycpanel_ipaddress)
                ? $service_fields->buycpanel_ipaddress
                : ''
            ),
            'ordertype' => (is_numeric($license) ? $license : '25'), // set license number, or 25 for an addon license
            'license_type' => $license
        ];

        // Add the service back with the same values
        $fields = $this->addService(
            $package,
            $vars,
            $parent_package,
            $parent_service,
            $status = 'active',
            ['allow_order_type' => true]
        );

        if (!empty($fields)) {
            return $fields;
        }
        return null;
    }

    /**
     * Allows the module to perform an action when the service is ready to renew.
     * Sets Input errors on failure, preventing the service from renewing.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being renewed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function renewService($package, $service, $parent_package = null, $parent_service = null)
    {
        // Nothing to do
        return null;
    }

    /**
     * Updates the package for the service on the remote server. Sets Input
     * errors on failure, preventing the service's package from being changed.
     *
     * @param stdClass $package_from A stdClass object representing the current package
     * @param stdClass $package_to A stdClass object representing the new package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent
     *  service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent
     *  service of the service being changed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically
     *  indexed array of meta fields to be stored for this service containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function changeServicePackage(
        $package_from,
        $package_to,
        $service,
        $parent_package = null,
        $parent_service = null
    ) {
        // Nothing to do
        return null;
    }

    /**
     * Validates input data when attempting to add a package, returns the meta
     * data to save when adding a package. Performs any action required to add
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being added.
     *
     * @param array An array of key/value pairs used to add the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addPackage(array $vars = null)
    {
        // Set rules to validate input data
        $this->Input->setRules($this->getPackageRules($vars));

        // Build meta data to return
        $meta = [];
        if ($this->Input->validates($vars)) {
            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = [
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                ];
            }
        }
        return $meta;
    }

    /**
     * Validates input data when attempting to edit a package, returns the meta
     * data to save when editing a package. Performs any action required to edit
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being edited.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array An array of key/value pairs used to edit the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editPackage($package, array $vars = null)
    {
        // Same as adding a package
        return $this->addPackage($vars);
    }

    /**
     * Deletes the package on the remote server. Sets Input errors on failure,
     * preventing the package from being deleted.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function deletePackage($package)
    {
        // Nothing to do
        return null;
    }

    /**
     * Returns the rendered view of the manage module page
     *
     * @param mixed $module A stdClass object representing the module and its rows
     * @param array $vars An array of post data submitted to or on the manage module page
     *  (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the manager module page
     */
    public function manageModule($module, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('manage', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        $this->view->set('module', $module);

        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the add module row page
     *
     * @param array $vars An array of post data submitted to or on the add
     *  module row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the add module row page
     */
    public function manageAddRow(array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('add_row', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        $this->view->set('vars', (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the edit module row page
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of post data submitted to or on the edit module
     *  row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the edit module row page
     */
    public function manageEditRow($module_row, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('edit_row', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        if (empty($vars)) {
            $vars = $module_row->meta;
        }

        $this->view->set('vars', (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Adds the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being added.
     *
     * @param array $vars An array of module info to add
     * @return array A numerically indexed array of meta fields for the module row containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function addModuleRow(array &$vars)
    {
        $meta_fields = ['email', 'key', 'test_mode'];
        $encrypted_fields = ['key'];

        if (!isset($vars['test_mode'])) {
            $vars['test_mode'] = 'false';
        }

        $this->Input->setRules($this->getRowRules($vars));

        // Validate module row
        if ($this->Input->validates($vars)) {
            // Build the meta data for this row
            $meta = [];
            foreach ($vars as $key => $value) {
                if (in_array($key, $meta_fields)) {
                    $meta[] = [
                        'key' => $key,
                        'value' => $value,
                        'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
                    ];
                }
            }

            return $meta;
        }
    }

    /**
     * Edits the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being updated.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of module info to update
     * @return array A numerically indexed array of meta fields for the module row containing:
     *  - key The key for this meta field
     *  - value The value for this key
     *  - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function editModuleRow($module_row, array &$vars)
    {
        // Same as adding
        return $this->addModuleRow($vars);
    }

    /**
     * Deletes the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being deleted.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     */
    public function deleteModuleRow($module_row)
    {
        // Nothing to do
        return null;
    }

    /**
     * Returns an array of available service delegation order methods. The module
     * will determine how each method is defined. For example, the method "first"
     * may be implemented such that it returns the module row with the least number
     * of services assigned to it.
     *
     * @return array An array of order methods in key/value pairs where the key is
     *  the type to be stored for the group and value is the name for that option
     * @see Module::selectModuleRow()
     */
    public function getGroupOrderOptions()
    {
        return ['first'=>Language::_('BuycPanel.order_options.first', true)];
    }

    /**
     * Determines which module row should be attempted when a service is provisioned
     * for the given group based upon the order method set for that group.
     *
     * @return int The module row ID to attempt to add the service with
     * @see Module::getGroupOrderOptions()
     */
    public function selectModuleRow($module_group_id)
    {
        if (!isset($this->ModuleManager)) {
            Loader::loadModels($this, ['ModuleManager']);
        }

        $group = $this->ModuleManager->getGroup($module_group_id);

        if ($group) {
            switch ($group->add_order) {
                default:
                case 'first':

                    foreach ($group->rows as $row) {
                        return $row->id;
                    }

                    break;
            }
        }
        return 0;
    }

    /**
     * Returns all fields used when adding/editing a package, including any
     * javascript to execute when the page is rendered with these fields.
     *
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containing the fields to render
     *  as well as any additional HTML markup to include
     */
    public function getPackageFields($vars = null)
    {
        Loader::loadHelpers($this, ['Html']);

        $fields = new ModuleFields();

        // Set the Order Types as selectable options
        $types = ['' => Language::_('BuycPanel.please_select', true)] + $this->getLicenseTypes();
        $license_type = $fields->label(Language::_('BuycPanel.package_fields.license_type', true), 'license_type');
        $license_type->attach(
            $fields->fieldSelect(
                'meta[license_type]',
                $types,
                $this->Html->ifSet($vars->meta['license_type']),
                ['id' => 'license_type']
            )
        );
        $fields->setField($license_type);

        return $fields;
    }

    /**
     * Returns an array of key values for fields stored for a module, package,
     * and service under this module, used to substitute those keys with their
     * actual module, package, or service meta values in related emails.
     *
     * @return array A multi-dimensional array of key/value pairs where each key is one
     *  of 'module', 'package', or 'service' and each value is a numerically indexed array
     *  of key values that match meta fields under that category.
     * @see Modules::addModuleRow()
     * @see Modules::editModuleRow()
     * @see Modules::addPackage()
     * @see Modules::editPackage()
     * @see Modules::addService()
     * @see Modules::editService()
     */
    public function getEmailTags()
    {
        return [
            'module' => [],
            'package' => [],
            'service' => ['buycpanel_ipaddress', 'buycpanel_domain', 'buycpanel_license']
        ];
    }

    /**
     * Returns all fields to display to an admin attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as
     *  well as any additional HTML markup to include
     */
    public function getAdminAddFields($package, $vars = null)
    {
        Loader::loadHelpers($this, ['Html']);

        $fields = new ModuleFields();

        $domain = $fields->label(Language::_('BuycPanel.service_fields.domain', true), 'buycpanel_domain');
        $domain->attach(
            $fields->fieldText(
                'buycpanel_domain',
                $this->Html->ifSet($vars->buycpanel_domain, $this->Html->ifSet($vars->domain)),
                ['id' => 'buycpanel_domain']
            )
        );
        // Add tooltip
        $tooltip = $fields->tooltip(Language::_('BuycPanel.service_field.tooltip.domain', true));
        $domain->attach($tooltip);
        $fields->setField($domain);

        // Set the IP address as selectable options
        $ip = $fields->label(Language::_('BuycPanel.service_fields.ipaddress', true), 'buycpanel_ipaddress');
        $ip->attach(
            $fields->fieldText(
                'buycpanel_ipaddress',
                $this->Html->ifSet($vars->buycpanel_ipaddress),
                ['id' => 'buycpanel_ipaddress']
            )
        );
        // Add tooltip
        $tooltip = $fields->tooltip(Language::_('BuycPanel.service_field.tooltip.ipaddress', true));
        $ip->attach($tooltip);
        $fields->setField($ip);

        return $fields;
    }

    /**
     * Returns all fields to display to a client attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render
     *  as well as any additional HTML markup to include
     */
    public function getClientAddFields($package, $vars = null)
    {
        // Same as admin fields
        return $this->getAdminAddFields($package, $vars);
    }

    /**
     * Returns all fields to display to an admin attempting to edit a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render
     *  as well as any additional HTML markup to include
     */
    public function getAdminEditFields($package, $vars = null)
    {
        Loader::loadHelpers($this, ['Html']);

        $fields = new ModuleFields();

        $domain = $fields->label(Language::_('BuycPanel.service_fields.domain', true), 'buycpanel_domain');
        $domain->attach(
            $fields->fieldText(
                'buycpanel_domain',
                $this->Html->ifSet($vars->buycpanel_domain),
                ['id' => 'buycpanel_domain']
            )
        );
        // Add tooltip
        $tooltip = $fields->tooltip(Language::_('BuycPanel.service_field.tooltip.domain_edit', true));
        $domain->attach($tooltip);
        $fields->setField($domain);

        // Set the IP address as selectable options
        $ip = $fields->label(Language::_('BuycPanel.service_fields.ipaddress', true), 'buycpanel_ipaddress');
        $ip->attach(
            $fields->fieldText(
                'buycpanel_ipaddress',
                $this->Html->ifSet($vars->buycpanel_ipaddress),
                ['id' => 'buycpanel_ipaddress']
            )
        );
        // Add tooltip
        $tooltip = $fields->tooltip(Language::_('BuycPanel.service_field.tooltip.ipaddress', true));
        $ip->attach($tooltip);
        $fields->setField($ip);

        return $fields;
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * admin interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getAdminServiceInfo($service, $package)
    {
        $row = $this->getModuleRow();

        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('admin_service_info', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html']);

        $this->view->set('module_row', $row);
        $this->view->set('package', $package);
        $this->view->set('service', $service);
        $this->view->set('service_fields', $this->serviceFieldsToObject($service->fields));
        $this->view->set('licenses', $this->getLicenseTypes());

        return $this->view->fetch();
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * client interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getClientServiceInfo($service, $package)
    {
        $row = $this->getModuleRow();

        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('client_service_info', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html']);

        $this->view->set('module_row', $row);
        $this->view->set('package', $package);
        $this->view->set('service', $service);
        $this->view->set('service_fields', $this->serviceFieldsToObject($service->fields));
        $this->view->set('licenses', $this->getLicenseTypes());

        return $this->view->fetch();
    }

    /**
     * Returns all tabs to display to a client when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title.
     *  Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getClientTabs($package)
    {
        return [
            'tabClientIp' => ['name' => Language::_('BuycPanel.tab_ip', true), 'icon' => 'fa fa-edit']
        ];
    }

    /**
     * Tab to allow clients to update their IP address for the license
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabClientIp($package, $service, array $get = null, array $post = null, array $files = null)
    {
        $this->view = new View('tab_client_ip', 'default');
        $this->view->base_uri = $this->base_uri;
        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html']);

        // Fetch the service fields
        $service_fields = $this->serviceFieldsToObject($service->fields);

        if (!empty($post)) {
            // Get module row and API
            $module_row = $this->getModuleRow();
            $api = $this->getApi(
                $module_row->meta->email,
                $module_row->meta->key,
                ($module_row->meta->test_mode == 'true')
            );

            $vars = [
                'buycpanel_ipaddress' => (isset($post['buycpanel_ipaddress']) ? $post['buycpanel_ipaddress'] : ''),
                'buycpanel_domain' => (isset($service_fields->buycpanel_domain)
                    ? $service_fields->buycpanel_domain
                    : ''
                )
            ];

            // Update the service IP address
            Loader::loadModels($this, ['Services']);
            $this->Services->edit($service->id, $vars);

            if ($this->Services->errors()) {
                $this->Input->setErrors($this->Services->errors());
            }

            $vars = $post;
        }

        // Set default vars
        if (empty($vars)) {
            $vars = [
                'buycpanel_ipaddress' => (isset($service_fields->buycpanel_ipaddress)
                    ? $service_fields->buycpanel_ipaddress
                    : ''
                )
            ];
        }

        $this->view->set('vars', (object)$vars);
        $this->view->set('service_fields', $service_fields);
        $this->view->set('service_id', $service->id);

        $this->view->set('view', $this->view->view);
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'buycpanel' . DS);
        return $this->view->fetch();
    }

    /**
     * Fetches accepted license types
     *
     * @return array A list of key/value pairs representing the license type and it's name
     */
    public function getLicenseTypes()
    {
        // Load product configuration required by this module
        Configure::load('buycpanel', dirname(__FILE__) . DS . 'config' . DS);
        $licenses = Configure::get('BuycPanel.licenses');

        $base_types = [];
        $license_types = [];
        foreach ($licenses as $license) {
            if (strpos($license, 'cpanelpremier') === 0) {
                // cPanel Premier Cloud/Metal are separated by the number of accounts included in the name
                $accounts = preg_replace('/[^0-9]/i', '', $license);
                $type = str_replace($accounts, '', $license);
                $license_types[$license] = Language::_('BuycPanel.license_types.' . $type, true, $accounts);
            } else {
                // All other licenses are as defined
                if (is_numeric($license)) {
                    // Add numeric license types to a separate array
                    $base_types[$license] = Language::_('BuycPanel.license_types.' . $license, true);
                } else {
                    $license_types[$license] = Language::_('BuycPanel.license_types.' . $license, true);
                }
            }
        }

        // Sort the licenses by value
        asort($license_types, SORT_FLAG_CASE | SORT_NATURAL);

        // Display the base types (i.e. numeric licenses) ahead of the others
        return $base_types + $license_types;
    }

    /**
     * Changes the IP address to a new IP address
     *
     * @param BuycpanelApi An stdClass object representing the API
     * @param string $current_ip The current service IP address
     * @param string $new_ip The IP address to set for the service
     */
    private function changeIp($api, $current_ip, $new_ip)
    {
        // Only change IP address
        $params = [
            'currentip' => $current_ip,
            'newip' => $new_ip
        ];

        try {
            // Change the IP address
            $command = new BuycpanelAll($api);
            $response = $command->changeIp($params);
            $this->processResponse($api, $response);
        } catch (Exception $e) {
            // Internal Error
            $this->Input->setErrors(['api' => ['internal' => Language::_('BuycPanel.!error.api.internal', true)]]);
        }
    }

    /**
     * Returns an array of service fields to set for the service using the given input
     *
     * @param array $vars An array of key/value input pairs
     * @param stdClass $package A stdClass object representing the package for the service
     * @return array An array of key/value pairs representing service fields
     */
    private function getFieldsFromInput(array $vars, $package)
    {
        $fields = [
            'serverip' => isset($vars['buycpanel_ipaddress']) ? $vars['buycpanel_ipaddress']: null,
            'domain' => isset($vars['buycpanel_domain']) ? strtolower($vars['buycpanel_domain']) : null,
            // Set the order type to 25 to signify it is an addon license
            'ordertype' => (isset($vars['ordertype'])
                ? $vars['ordertype']
                : (isset($package->meta->license_type) && is_numeric($package->meta->license_type)
                    ? $package->meta->license_type
                    : '25'
                )
            )
        ];

        // If the order type is an addon, the license type should be set
        if ($fields['ordertype'] == '25') {
            $key = (isset($vars['license_type'])
                ? $vars['license_type']
                : (isset($package->meta->license_type) ? $package->meta->license_type : '')
            );
            $fields['addon'] = [$key => '1'];
        }

        return $fields;
    }

    /**
     * Validates that the given hostname is valid
     *
     * @param string $host_name The host name to validate
     * @return bool True if the hostname is valid, false otherwise
     */
    public function validateHostName($host_name)
    {
        if (strlen($host_name) > 255) {
            return false;
        }

        return $this->Input->matches(
            $host_name,
            "/^([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9])(\.([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9]))+$/i"
        );
    }

    /**
     * Process API response, setting any errors, and logging the request
     *
     * @param BuycpanelApi $api The BuycPanel API object
     * @param BuycpanelResponse $response The BuycPanel API response object
     */
    private function processResponse(BuycpanelApi $api, BuycpanelResponse $response)
    {
        $this->logRequest($api, $response);

        // Set errors, if any
        if ($response->status() != '1') {
            $errors = $response->errors() ? $response->errors() : [];
            $this->Input->setErrors(['errors' => $errors]);
        }
    }

    /**
     * Logs the API request
     *
     * @param BuycpanelApi $api The BuycPanel API object
     * @param BuycpanelResponse $response The BuycPanel API response object
     */
    private function logRequest(BuycpanelApi $api, BuycpanelResponse $response)
    {
        $last_request = $api->lastRequest();
        $last_request['args']['key'] = '***';

        $this->log($last_request['url'], serialize($last_request['args']), 'input', true);
        $this->log($last_request['url'], $response->raw(), 'output', $response->status() == '1');
    }

    /**
     * Initializes the BuycPanel Api and returns an instance of that object with the given account information set
     *
     * @param string $email The account email address
     * @param string $key The API Key
     * @return BuycpanelApi A BuycpanelApi instance
     */
    private function getApi($email, $key, $test_mode)
    {
        Loader::load(dirname(__FILE__) . DS . 'apis' . DS . 'buycpanel_api.php');

        return new BuycpanelApi($email, $key, $test_mode);
    }

    /**
     * Builds and returns the rules required to add/edit a module row (e.g. server)
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    private function getRowRules(&$vars)
    {
        return [
            'email' => [
                'valid' => [
                    'rule' => 'isEmail',
                    'message' => Language::_('BuycPanel.!error.email.valid', true)
                ]
            ],
            'key' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('BuycPanel.!error.key.empty', true)
                ]
            ],
            'test_mode' => [
                'valid' => [
                    'if_set' => true,
                    'rule' => ['in_array', ['true', 'false']],
                    'message' => Language::_('BuycPanel.!error.test_mode.valid', true)
                ]
            ]
        ];
    }

    /**
     * Bulids and returns the rules required for validating packages
     *
     * @param array $vars An array of key/value pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    private function getPackageRules($vars)
    {
        // Convert integer types to string
        $license_types = array_keys($this->getLicenseTypes());
        foreach ($license_types as &$type) {
            $type = (string)$type;
        }

        return [
            'meta[license_type]' => [
                'valid' => [
                    'rule' => ['in_array', $license_types],
                    'message' => Language::_('BuycPanel.!error.meta[license_type].valid', true)
                ]
            ]
        ];
    }
}
