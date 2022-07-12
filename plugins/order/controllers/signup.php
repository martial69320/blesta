<?php

use Blesta\Core\Util\Captcha\CaptchaFactory;

/**
 * Order System signup controller
 *
 * @package blesta
 * @subpackage blesta.plugins.order.controllers
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Signup extends OrderFormController
{
    /**
     * Signup
     */
    public function index()
    {
        $vars = new stdClass();

        $this->uses(['Users', 'Contacts', 'Countries', 'States', 'ClientGroups']);
        $this->components(['SettingsCollection']);
        $this->ArrayHelper = $this->DataStructure->create('Array');
        $requestor = $this->getFromContainer('requestor');

        $order_settings = $this->ArrayHelper->numericToKey(
            $this->OrderSettings->getSettings($this->company_id),
            'key',
            'value'
        );

        // Get company settings
        $company_settings = $this->SettingsCollection->fetchSettings($this->Companies, $this->company_id);

        // Check if captcha is required for signups
        $require_captcha = (
            $this->order_form->require_captcha == '1'
            && isset($order_settings['captcha'])
        );

        $captcha = null;
        if ($require_captcha && $order_settings['captcha'] == 'recaptcha') {
            $captcha = $this->getCaptcha(
                [
                    'site_key' => $order_settings['recaptcha_pub_key'],
                    'shared_key' => $order_settings['recaptcha_shared_key'],
                    'lang' => substr($company_settings['language'], 0, 2),
                    'ip_address' => $requestor->ip_address
                ]
            );
        }

        // Fetch client group tax ID setting
        $show_client_tax_id = $this->SettingsCollection->fetchClientGroupSetting(
            $this->order_form->client_group_id,
            null,
            'show_client_tax_id'
        );
        $show_client_tax_id = (isset($show_client_tax_id['value']) ? $show_client_tax_id['value'] : '');

        // Set default currency, country, and language settings from this company
        $vars = new stdClass();
        $vars->country = $company_settings['country'];

        if (!empty($this->post)) {
            $errors = false;

            if ($captcha !== null) {
                $success = false;

                if ($order_settings['captcha'] == 'recaptcha') {
                    $response = (isset($this->post['g-recaptcha-response']) ? $this->post['g-recaptcha-response'] : '');
                    $success = $captcha->verify(['response' => $response]);
                }

                if (!$success) {
                    $errors = ['captcha' => ['invalid' => Language::_('Signup.!error.captcha.invalid', true)]];
                }
            }

            if (!$errors) {
                // Set mandatory defaults
                $this->post['client_group_id'] = $this->order_form->client_group_id;

                $client_info = $this->post;
                $client_info['settings'] = [
                    'username_type' => $this->post['username_type'],
                    'tax_id' => ($show_client_tax_id == 'true' ? $this->post['tax_id'] : ''),
                    'default_currency' => $this->SessionCart->getData('currency'),
                    'language' => $company_settings['language'],
                    'receive_email_marketing' => (isset($this->post['receive_email_marketing'])
                        ? $this->post['receive_email_marketing']
                        : 'false'
                    )
                ];
                $client_info['numbers'] = $this->ArrayHelper->keyToNumeric($client_info['numbers']);

                foreach ($this->post as $key => $value) {
                    if (substr($key, 0, strlen($this->custom_field_prefix)) == $this->custom_field_prefix) {
                        $client_info['custom'][str_replace($this->custom_field_prefix, '', $key)] = $value;
                    }
                }

                // Fraud detection
                if (!empty($order_settings['antifraud'])) {
                    $errors = $this->runAntifraudCheck($order_settings, (object)$client_info);
                }

                if (!$errors) {
                    // Create the client
                    $this->client = $this->Clients->create($client_info);

                    $errors = $this->Clients->errors();
                }
            }

            if ($errors) {
                $this->setMessage('error', $errors, false, null, false);
            } else {
                // Log the user into the newly created client account
                $login = [
                    'username' => $this->client->username,
                    'password' => $client_info['new_password']
                ];
                $user_id = $this->Users->login($this->Session, $login);

                if ($user_id) {
                    $this->Session->write('blesta_company_id', $this->company_id);
                    $this->Session->write('blesta_client_id', $this->client->id);
                }

                if (!$this->isAjax()) {
                    $this->redirect($this->base_uri . 'order/checkout/index/' . $this->order_form->label);
                }
            }
            $vars = (object)$this->post;
        } elseif (($geo_location = $this->getGeoIp($requestor->ip_address)) && $geo_location['location']) {
            $vars->country = $geo_location['location']['country_code'];
            $vars->state = $geo_location['location']['region'];
        }


        // Set custom fields to display
        $custom_fields = $this->Clients->getCustomFields(
            $this->company_id,
            $this->order_form->client_group_id,
            ['show_client' => 1]
        );

        // Swap key/value pairs for "Select" option custom fields (to display)
        foreach ($custom_fields as &$field) {
            if ($field->type == 'select' && is_array($field->values)) {
                $field->values = array_flip($field->values);
            }
        }

        // Default the client's option to receive marketing emails base on the order plugin setting
        if (!isset($vars->receive_email_marketing) && isset($order_settings['marketing_default'])) {
            $vars->receive_email_marketing = $order_settings['marketing_default'];
        }

        $show_recieve_email_marketing = $this->SettingsCollection->fetchClientGroupSetting(
            $this->client ? $this->client->client_group_id : $this->order_form->client_group_id,
            $this->ClientGroups,
            'show_receive_email_marketing'
        );

        $this->set('custom_field_prefix', $this->custom_field_prefix);
        $this->set('custom_fields', $custom_fields);

        $this->set(
            'countries',
            $this->Form->collapseObjectArray($this->Countries->getList(), ['name', 'alt_name'], 'alpha2', ' - ')
        );
        $this->set('states', $this->Form->collapseObjectArray($this->States->getList($vars->country), 'name', 'code'));
        $this->set('currencies', $this->Currencies->getAll($this->company_id));

        $this->set('vars', $vars);

        $this->set('client', $this->client);
        if (!$this->isClientOwner($this->client, $this->Session)) {
            $this->setMessage('error', Language::_('Signup.!error.not_client_owner', true), false, null, false);
            $this->set('client', false);
        }
        $this->set('show_client_tax_id', ($show_client_tax_id == 'true'));
        $this->set(
            'show_receive_email_marketing',
            $show_recieve_email_marketing ? $show_recieve_email_marketing['value'] : 'true'
        );
        $this->set('captcha', ($captcha !== null ? $captcha->buildHtml() : ''));

        return $this->renderView();
    }

    /**
     * Outputs clients info
     */
    public function clientinfo()
    {
        $this->set('client', $this->Clients->get($this->Session->read('blesta_client_id')));
        $this->outputAsJson($this->view->fetch());
        return false;
    }

    /**
     * AJAX Fetch all states belonging to a given country (json encoded ajax request)
     */
    public function getStates()
    {
        $this->uses(['States']);
        $states = [];
        if (isset($this->get[1])) {
            $states = (array)$this->Form->collapseObjectArray($this->States->getList($this->get[1]), 'name', 'code');
        }

        $this->outputAsJson($states);
        return false;
    }

    /**
     * Retrieve an instance of the captcha
     *
     * @return Blesta\Core\Util\Captcha\Common\CaptchaInterface
     */
    private function getCaptcha(array $options)
    {
        $factory = new CaptchaFactory();

        // Retrieve reCaptcha
        return $factory->reCaptcha($options);
    }
}
