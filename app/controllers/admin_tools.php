<?php

/**
 * Admin Tools
 *
 * @package blesta
 * @subpackage blesta.app.controllers
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminTools extends AppController
{
    /**
     * Tools pre-action
     */
    public function preAction()
    {
        parent::preAction();

        // Require login
        $orig_action = $this->action;
        if (substr($this->action, 0, 3) == 'log') {
            $this->action = 'logs';
        }

        $this->requireLogin();
        $this->action = $orig_action;

        $this->uses(['Logs']);
        Language::loadLang(['admin_tools']);
    }

    /**
     * Index
     */
    public function index()
    {
        // Default to logs (module log)
        $this->redirect($this->base_uri . 'tools/logs/module/');
    }

    /**
     * All logs
     */
    public function logs()
    {
        // Default to module log
        $this->redirect($this->base_uri . 'tools/logs/module/');
    }

    /**
     * List module log data
     */
    public function logModule()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_added');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        // Fetch all the module log groups
        $module_logs = $this->Logs->getModuleList($page, [$sort => $order], true);

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('module_logs', $module_logs);
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getModuleListCount(true),
                'uri' => $this->base_uri . 'tools/logs/module/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * AJAX request for all module log data under a specific module log group
     */
    public function moduleLogList()
    {
        if (!isset($this->get[0]) || !$this->isAjax()) {
            header($this->server_protocol . ' 401 Unauthorized');
            exit();
        }

        $vars = [
            'module_logs' => $this->Logs->getModuleGroupList($this->get[0])
        ];
        // Fetch module logs for a specific group and send the template
        echo $this->partial('admin_tools_moduleloglist', $vars);

        // Render without layout
        return false;
    }

    /**
     * List gateway log data
     */
    public function logGateway()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_added');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        // Fetch all the gateway log groups
        $gateway_logs = $this->Logs->getGatewayList($page, [$sort => $order], true);

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('gateway_logs', $gateway_logs);
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getGatewayListCount(true),
                'uri' => $this->base_uri . 'tools/logs/gateway/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * AJAX request for all gateway log data under a specific gateway log group
     */
    public function gatewayLogList()
    {
        if (!isset($this->get[0]) || !$this->isAjax()) {
            header($this->server_protocol . ' 401 Unauthorized');
            exit();
        }

        $vars = [
            'gateway_logs' => $this->Logs->getGatewayGroupList($this->get[0])
        ];
        // Fetch module logs for a specific group and send the template
        echo $this->partial('admin_tools_gatewayloglist', $vars);

        // Render without layout
        return false;
    }

    /**
     * List all email log data
     */
    public function logEmail()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_sent');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        // Fetch all the module log groups
        $email_logs = $this->Logs->getEmailList($page, [$sort => $order], true);

        // Format CC addresses, if available
        if ($email_logs) {
            for ($i = 0, $num_logs = count($email_logs); $i < $num_logs; $i++) {
                // Format all CC addresses from CSV to array
                $cc_addresses = $email_logs[$i]->cc_address;
                $email_logs[$i]->cc_address = [];
                foreach (explode(',', $cc_addresses) as $address) {
                    if (!empty($address)) {
                        $email_logs[$i]->cc_address[] = $address;
                    }
                }
            }
        }

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('email_logs', $email_logs);
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getEmailListCount(true),
                'uri' => $this->base_uri . 'tools/logs/email/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all user log data
     */
    public function logUsers()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_added');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $user_logs = $this->Logs->getUserList($page, [$sort => $order]);

        if (!isset($this->SettingsCollection)) {
            $this->components(['SettingsCollection']);
        }

        // Check whether GeoIp is enabled
        $system_settings = $this->SettingsCollection->fetchSystemSettings();
        $use_geo_ip = ($system_settings['geoip_enabled'] == 'true');
        if ($use_geo_ip) {
            // Load GeoIP database
            $this->components(['Net']);
            if (!isset($this->NetGeoIp)) {
                $this->NetGeoIp = $this->Net->create('NetGeoIp');
            }
        }

        foreach ($user_logs as &$user) {
            $user->geo_ip = [];
            if ($use_geo_ip) {
                try {
                    $user->geo_ip = ['location' => $this->NetGeoIp->getLocation($user->ip_address)];
                } catch (Exception $e) {
                    // Nothing to do
                }
            }
        }

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('user_logs', $user_logs);
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getUserListCount(),
                'uri' => $this->base_uri . 'tools/logs/users/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all contact log data
     */
    public function logContacts()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_changed');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('contact_logs', $this->Logs->getContactList($page, [$sort => $order]));
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getContactListCount(),
                'uri' => $this->base_uri . 'tools/logs/contacts/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all client settings log data
     */
    public function logClientSettings()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_changed');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $client_settings_logs = $this->Logs->getClientSettingsList($page, [$sort => $order]);

        if (!isset($this->SettingsCollection)) {
            $this->components(['SettingsCollection']);
        }

        // Check whether GeoIp is enabled
        $system_settings = $this->SettingsCollection->fetchSystemSettings();
        $use_geo_ip = ($system_settings['geoip_enabled'] == 'true');
        if ($use_geo_ip) {
            // Load GeoIP database
            $this->components(['Net']);
            if (!isset($this->NetGeoIp)) {
                $this->NetGeoIp = $this->Net->create('NetGeoIp');
            }
        }

        foreach ($client_settings_logs as &$setting_log) {
            $setting_log->geo_ip = [];
            if ($use_geo_ip) {
                try {
                    $setting_log->geo_ip = ['location' => $this->NetGeoIp->getLocation($setting_log->ip_address)];
                } catch (Exception $e) {
                    // Nothing to do
                }
            }
        }

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('client_settings_logs', $client_settings_logs);
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getClientSettingsListCount(),
                'uri' => $this->base_uri . 'tools/logs/clientsettings/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all transaction log data
     */
    public function logTransactions()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_changed');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('transaction_logs', $this->Logs->getTransactionList($page, [$sort => $order]));
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getTransactionListCount(),
                'uri' => $this->base_uri . 'tools/logs/transactions/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all invoice delivery log data
     */
    public function logInvoiceDelivery()
    {
        $this->uses(['Invoices']);

        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_sent');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('invoice_logs', $this->Invoices->getDeliveryList(null, $page, [$sort => $order]));
        $this->set('link_tabs', $this->getLogNames());
        $this->set('invoice_methods', $this->Invoices->getDeliveryMethods(null, null, false));

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Invoices->getDeliveryListCount(),
                'uri' => $this->base_uri . 'tools/logs/invoicedelivery/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * List all account access log data
     */
    public function logAccountAccess()
    {
        // When/who unencrypted credit cards

        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'date_accessed');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('access_logs', $this->Logs->getAccountAccessList($page, [$sort => $order]));
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getAccountAccessListCount(),
                'uri' => $this->base_uri . 'tools/logs/accountaccess/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * AJAX request for all account access log data
     */
    public function accountAccess()
    {
        if (!isset($this->get[0]) || !$this->isAjax()) {
            header($this->server_protocol . ' 401 Unauthorized');
            exit();
        }

        $this->uses(['Accounts']);

        $vars = [
            'access_logs' => $this->Logs->getAccountAccessLog($this->get[0]),
            'account_types' => $this->Accounts->getTypes(),
            'cc_types' => $this->Accounts->getCcTypes(),
            'ach_types' => $this->Accounts->getAchTypes()
        ];
        // Fetch module logs for a specific group and send the template
        echo $this->partial('admin_tools_accountaccess', $vars);

        // Render without layout
        return false;
    }

    /**
     * List all cron log data
     */
    public function logCron()
    {
        $page = (isset($this->get[0]) ? (int) $this->get[0] : 1);
        $sort = (isset($this->get['sort']) ? $this->get['sort'] : 'start_date');
        $order = (isset($this->get['order']) ? $this->get['order'] : 'desc');

        $this->set('sort', $sort);
        $this->set('order', $order);
        $this->set('negate_order', ($order == 'asc' ? 'desc' : 'asc'));
        $this->set('cron_logs', $this->Logs->getCronList($page, [$sort => $order]));
        $this->set('link_tabs', $this->getLogNames());

        // Overwrite default pagination settings
        $settings = array_merge(
            Configure::get('Blesta.pagination'),
            [
                'total_results' => $this->Logs->getCronListCount(),
                'uri' => $this->base_uri . 'tools/logs/cron/[p]/',
                'params' => ['sort' => $sort, 'order' => $order]
            ]
        );
        $this->setPagination($this->get, $settings);

        return $this->renderAjaxWidgetIfAsync(isset($this->get[0]) || isset($this->get['sort']));
    }

    /**
     * Retrieves a list of link tabs for use in templates
     */
    private function getLogNames()
    {
        return [
            ['name' => Language::_('AdminTools.getlognames.text_module', true), 'uri' => 'module'],
            ['name' => Language::_('AdminTools.getlognames.text_gateway', true), 'uri' => 'gateway'],
            ['name' => Language::_('AdminTools.getlognames.text_email', true), 'uri' => 'email'],
            ['name' => Language::_('AdminTools.getlognames.text_users', true), 'uri' => 'users'],
            ['name' => Language::_('AdminTools.getlognames.text_contacts', true), 'uri' => 'contacts'],
            ['name' => Language::_('AdminTools.getlognames.text_client_settings', true), 'uri' => 'clientsettings'],
            ['name' => Language::_('AdminTools.getlognames.text_accountaccess', true), 'uri' => 'accountaccess'],
            ['name' => Language::_('AdminTools.getlognames.text_transactions', true), 'uri' => 'transactions'],
            ['name' => Language::_('AdminTools.getlognames.text_cron', true), 'uri' => 'cron'],
            ['name' => Language::_('AdminTools.getlognames.text_invoice_delivery', true), 'uri' => 'invoicedelivery'],
        ];
    }

    /**
     * Currency conversion
     */
    public function convertCurrency()
    {
        $this->uses(['Currencies']);
        $this->components(['SettingsCollection']);

        $vars = new stdClass();

        // Set current default currency
        $default_currency = $this->SettingsCollection->fetchSetting(null, $this->company_id, 'default_currency');
        $vars->to_currency = $default_currency['value'];

        // Do the conversion
        if (!empty($this->post)) {
            $vars = (object) $this->post;

            // Convert the currency
            $amount = (isset($this->post['amount']) ? $this->post['amount'] : 0);
            $to_currency = (isset($this->post['to_currency']) ? $this->post['to_currency'] : '');
            $from_currency = (isset($this->post['from_currency']) ? $this->post['from_currency'] : '');
            $converted_amount = $this->Currencies->convert($amount, $from_currency, $to_currency, $this->company_id);

            $this->setMessage(
                'message',
                Language::_(
                    'AdminTools.!success.currency_converted',
                    true,
                    $this->Currencies->toCurrency($amount, $from_currency, $this->company_id, true, true, true),
                    $this->Currencies->toCurrency($converted_amount, $to_currency, $this->company_id, true, true, true)
                )
            );
        }

        $this->set(
            'currencies',
            $this->Form->collapseObjectArray($this->Currencies->getAll($this->company_id), 'code', 'code')
        );
        $this->set('vars', $vars);
    }
}
