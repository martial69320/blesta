<?php
/**
 * Order System plugin handler
 *
 * @package blesta
 * @subpackage blesta.plugins.order
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class OrderPlugin extends Plugin
{
    /**
     * Construct
     */
    public function __construct()
    {
        Language::loadLang('order_plugin', null, dirname(__FILE__) . DS . 'language' . DS);

        // Load components required by this plugin
        Loader::loadComponents($this, ['Input', 'Record']);

        $this->loadConfig(dirname(__FILE__) . DS . 'config.json');
    }

    /**
     * Performs any necessary bootstraping actions
     *
     * @param int $plugin_id The ID of the plugin being installed
     */
    public function install($plugin_id)
    {
        Loader::loadModels($this, ['CronTasks', 'Emails', 'EmailGroups', 'Languages', 'Permissions']);
        Configure::load('order', dirname(__FILE__) . DS . 'config' . DS);

        try {
            // order_forms
            $this->Record->
                setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])->
                setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('label', ['type' => 'varchar', 'size' => 32])->
                setField('name', ['type' => 'varchar', 'size' => 128])->
                setField('description', ['type' => 'text'])->
                setField('template', ['type' => 'varchar', 'size' => 64])->
                setField('template_style', ['type' => 'varchar', 'size' => 64])->
                setField('type', ['type' => 'varchar', 'size' => 64])->
                setField('client_group_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('manual_review', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setField('allow_coupons', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setField('require_ssl', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setField('require_captcha', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setField('require_tos', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setField('tos_url', ['type' => 'varchar', 'size' => 255, 'is_null' => true, 'default' => null])->
                setField('status', ['type' => 'enum', 'size' => "'active','inactive'", 'default' => 'active'])->
                setField(
                    'visibility',
                    ['type' => 'enum', 'size' => "'public','shared','client'", 'default' => 'shared']
                )->
                setField('date_added', ['type' => 'datetime'])->
                setKey(['id'], 'primary')->
                setKey(['label', 'company_id'], 'unique')->
                setKey(['status'], 'index')->
                setKey(['company_id'], 'index')->
                create('order_forms', true);

            // order_form_groups
            $this->Record->
                setField('order_form_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('package_group_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField(
                    'order',
                    ['type' => 'smallint', 'size' => 5, 'unsigned' => true, 'is_null' => false, 'default' => 0]
                )->
                setKey(['order_form_id', 'package_group_id'], 'primary')->
                create('order_form_groups', true);

            // order_form_meta
            $this->Record->
                setField('order_form_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('key', ['type' => 'varchar', 'size' => 32])->
                setField('value', ['type' => 'text'])->
                setKey(['order_form_id', 'key'], 'primary')->
                create('order_form_meta', true);

            // order_form_currencies
            $this->Record->
                setField('order_form_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('currency', ['type' => 'char', 'size' => 3])->
                setKey(['order_form_id', 'currency'], 'primary')->
                create('order_form_currencies', true);

            // order_form_gateways
            $this->Record->
                setField('order_form_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('gateway_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setKey(['order_form_id', 'gateway_id'], 'primary')->
                create('order_form_gateways', true);

            // order_staff_settings
            $this->Record->
                setField('staff_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('key', ['type' => 'varchar', 'size' => 32])->
                setField('value', ['type' => 'text'])->
                setKey(['staff_id', 'company_id', 'key'], 'primary')->
                create('order_staff_settings', true);

            // order_settings
            $this->Record->
                setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('key', ['type' => 'varchar', 'size' => 32])->
                setField('value', ['type' => 'text'])->
                setField('encrypted', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                setKey(['key', 'company_id'], 'primary')->
                create('order_settings', true);

            // orders
            $this->Record->
                setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])->
                setField('order_number', ['type' => 'varchar', 'size' => 16])->
                setField('order_form_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('invoice_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('fraud_report', ['type' => 'text', 'is_null' => true, 'default' => null])->
                setField(
                    'fraud_status',
                    ['type' => 'enum', 'size' => "'allow','review','reject'", 'is_null' => true, 'default' => null]
                )->
                setField(
                    'status',
                    ['type' => 'enum', 'size' => "'pending','accepted','fraud','canceled'", 'default' => 'pending']
                )->
                setField(
                    'ip_address',
                    ['type' => 'varchar', 'size' => 39, 'is_null' => true, 'default' => null]
                )->
                setField('date_added', ['type' => 'datetime'])->
                setKey(['id'], 'primary')->
                setKey(['order_number'], 'unique')->
                setKey(['order_form_id'], 'index')->
                setKey(['invoice_id'], 'index')->
                setKey(['status'], 'index')->
                create('orders', true);

            // order_services
            $this->Record->
                setField('order_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setField('service_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])->
                setKey(['order_id', 'service_id'], 'primary')->
                create('order_services', true);
        } catch (Exception $e) {
            // Error adding... no permission?
            $this->Input->setErrors(['db' => ['create' => $e->getMessage()]]);
            return;
        }


        // Add a cron task so we can check for incoming email tickets
        $task = [
            'key' => 'accept_paid_orders',
            'dir' => 'order',
            'task_type' => 'plugin',
            'name' => Language::_('OrderPlugin.cron.accept_paid_orders_name', true),
            'description' => Language::_('OrderPlugin.cron.accept_paid_orders_desc', true),
            'type' => 'interval'
        ];
        $task_id = $this->CronTasks->add($task);

        if (!$task_id) {
            $cron_task = $this->CronTasks->getByKey($task['key'], $task['dir'], $task['task_type']);
            if ($cron_task) {
                $task_id = $cron_task->id;
            }
        }

        if ($task_id) {
            $this->CronTasks->addTaskRun($task_id, [
                'interval' => 5,
                'enabled' => 1
            ]);
        }

        // Fetch all currently-installed languages for this company, for which email templates should be created for
        $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

        // Add all email templates
        $emails = Configure::get('Order.install.emails');
        foreach ($emails as $email) {
            $group = $this->EmailGroups->getByAction($email['action']);
            if ($group) {
                $group_id = $group->id;
            } else {
                $group_id = $this->EmailGroups->add([
                    'action' => $email['action'],
                    'type' => $email['type'],
                    'plugin_dir' => $email['plugin_dir'],
                    'tags' => $email['tags']
                ]);
            }

            // Set from hostname to use that which is configured for the company
            if (isset(Configure::get('Blesta.company')->hostname)) {
                $email['from'] = str_replace(
                    '@mydomain.com',
                    '@' . Configure::get('Blesta.company')->hostname,
                    $email['from']
                );
            }

            // Add the email template for each language
            foreach ($languages as $language) {
                $this->Emails->add([
                    'email_group_id' => $group_id,
                    'company_id' => Configure::get('Blesta.company_id'),
                    'lang' => $language->code,
                    'from' => $email['from'],
                    'from_name' => $email['from_name'],
                    'subject' => $email['subject'],
                    'text' => $email['text'],
                    'html' => $email['html']
                ]);
            }
        }

        // Add a new permission to the group
        $group = $this->Permissions->getGroupByAlias('admin_packages');
        $perm = [
            'plugin_id' => $plugin_id,
            'group_id' => $group->id,
            'name' => Language::_('OrderPlugin.admin_forms.name', true),
            'alias' => 'order.admin_forms',
            'action' => '*'
        ];
        $this->Permissions->add($perm);

        // Add a new permission for the order widget
        $group = $this->Permissions->getGroupByAlias('admin_billing');
        $perm = [
            'plugin_id' => $plugin_id,
            'group_id' => $group->id,
            'name' => Language::_('OrderPlugin.admin_main.name', true),
            'alias' => 'order.admin_main',
            'action' => '*'
        ];
        $this->Permissions->add($perm);

        if (($errors = $this->Permissions->errors())) {
            $this->Input->setErrors($errors);
            return;
        }
    }

    /**
     * Performs migration of data from $current_version (the current installed version)
     * to the given file set version
     *
     * @param string $current_version The current installed version of this plugin
     * @param int $plugin_id The ID of the plugin being upgraded
     */
    public function upgrade($current_version, $plugin_id)
    {
        Configure::load('order', dirname(__FILE__) . DS . 'config' . DS);

        // Upgrade if possible
        if (version_compare($this->getVersion(), $current_version, '>')) {
            // Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered
            if (version_compare($current_version, '1.1.0', '<')) {
                $this->Record->
                    setField('require_captcha', ['type' => 'tinyint', 'size' => 1, 'default' => 0])->
                    alter('order_forms');
                $this->Record->
                    setField(
                        'fraud_status',
                        ['type' => 'enum', 'size' => "'allow','review','reject'", 'is_null' => true, 'default' => null]
                    )->
                    alter('orders');
            }

            // Upgrade to 1.1.7
            if (version_compare($current_version, '1.1.7', '<')) {
                Loader::loadModels($this, ['Emails', 'EmailGroups', 'Languages']);

                // Add emails missing in additional languages that have been installed before the plugin was installed
                $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

                // Add all email templates in other languages IFF they do not already exist
                $emails = Configure::get('Order.install.emails');
                foreach ($emails as $email) {
                    $group = $this->EmailGroups->getByAction($email['action']);
                    if ($group) {
                        $group_id = $group->id;
                    } else {
                        $group_id = $this->EmailGroups->add([
                            'action' => $email['action'],
                            'type' => $email['type'],
                            'plugin_dir' => $email['plugin_dir'],
                            'tags' => $email['tags']
                        ]);
                    }

                    // Set from hostname to use that which is configured for the company
                    if (isset(Configure::get('Blesta.company')->hostname)) {
                        $email['from'] = str_replace(
                            '@mydomain.com',
                            '@' . Configure::get('Blesta.company')->hostname,
                            $email['from']
                        );
                    }

                    // Add the email template for each language
                    foreach ($languages as $language) {
                        // Check if this email already exists for this language
                        $template = $this->Emails->getByType(
                            Configure::get('Blesta.company_id'),
                            $email['action'],
                            $language->code
                        );

                        // Template already exists for this language
                        if ($template !== false) {
                            continue;
                        }

                        // Add the missing email for this language
                        $this->Emails->add([
                            'email_group_id' => $group_id,
                            'company_id' => Configure::get('Blesta.company_id'),
                            'lang' => $language->code,
                            'from' => $email['from'],
                            'from_name' => $email['from_name'],
                            'subject' => $email['subject'],
                            'text' => $email['text'],
                            'html' => $email['html']
                        ]);
                    }
                }
            }
            // Upgrade to 2.0.0
            if (version_compare($current_version, '2.0.0', '<')) {
                $this->Record->query(
                    "ALTER TABLE `orders` CHANGE `status` `status`
                    ENUM('pending', 'accepted', 'fraud', 'canceled') NOT NULL DEFAULT 'pending'"
                );
                $this->Record->query(
                    'ALTER TABLE `order_forms` ADD `template_style` VARCHAR( 64 ) NOT NULL AFTER `template`'
                );
                $this->Record->update('order_forms', ['template_style' => 'default']);
            }
            // Update to 2.2.2
            if (version_compare($current_version, '2.2.2', '<')) {
                // Convert default order form label to ID
                $this->Record->
                    from('order_forms')->
                    set('order_settings.value', 'order_forms.id', false)->
                    where('order_settings.key', '=', 'default_form')->
                    where('order_settings.value', '=', 'order_forms.label', false)->
                    where('order_settings.company_id', '=', 'order_forms.company_id', false)->
                    update('order_settings');
            }

            // Update to 2.10.0
            if (version_compare($current_version, '2.10.0', '<')) {
                Loader::loadModels($this, ['Emails', 'EmailGroups', 'Languages']);
                $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

                // Add IP address to order
                $this->Record->query(
                    'ALTER TABLE `orders` ADD `ip_address` VARCHAR(39) NULL DEFAULT NULL AFTER `status`'
                )->closeCursor();

                // Update email templates to include the IP address
                $emails = Configure::get('Order.install.emails');
                foreach ($emails as $email) {
                    // Only update the order received templates
                    if (!in_array($email['action'], ['Order.received', 'Order.received_mobile'])) {
                        continue;
                    }

                    // Update each email template in this language
                    foreach ($languages as $language) {
                        $template = $this->Emails->getByType(
                            Configure::get('Blesta.company_id'),
                            $email['action'],
                            $language->code
                        );

                        // Missing template
                        if (!$template) {
                            continue;
                        }

                        $vars = [
                            'text' => str_replace(
                                'Amount: {invoice.total} {order.currency}{% if order.fraud_status !="" %}',
                                "Amount: {invoice.total} {order.currency}\n"
                                . "IP Address: {order.ip_address}{% if order.fraud_status !=\"\" %}",
                                $template->text
                            ),
                            'html' => str_replace(
                                'Amount: {invoice.total} {order.currency}{% if order.fraud_status !="" %}',
                                "Amount: {invoice.total} {order.currency}<br />\n"
                                . "IP Address: {order.ip_address}{% if order.fraud_status !=\"\" %}",
                                $template->html
                            )
                        ];

                        // Update the email template
                        if ($template->text != $vars['text'] || $template->html != $vars['html']) {
                            $this->Record->where('id', '=', $template->id)
                                ->update('emails', $vars, ['text', 'html']);
                        }
                    }
                }

                // Add visibility
                $this->Record->query(
                    "ALTER TABLE `order_forms` ADD `visibility`
                    ENUM( 'public', 'shared', 'client' ) NOT NULL
                    DEFAULT 'shared' AFTER `status` "
                );

                // Add description
                $this->Record->query(
                    "ALTER TABLE `order_forms` ADD `description`
                    TEXT NOT NULL AFTER `name` "
                );
            }

            // Update to 2.11.2
            if (version_compare($current_version, '2.11.2', '<')) {
                // Update all order records to JSON-encode the fraud report. This replaces previous serialization
                $this->upgrade2_11_2();
            }

            // Update to 2.13.0
            if (version_compare($current_version, '2.13.0', '<')) {
                // Add order form package group order
                $this->Record->query(
                    "ALTER TABLE `order_form_groups` ADD `order`
                    SMALLINT(5) NOT NULL DEFAULT 0 AFTER `package_group_id`"
                );
            }
        }
    }

    /**
     * Upgrades the Order plugin to v2.11.2
     */
    private function upgrade2_11_2()
    {
        Loader::loadComponents($this, ['Json']);

        $start = 0;
        $batch_size = 50;
        $i = 1;

        while (true) {
            // Fetch orders until we have no more to update
            $orders = $this->getOrders($start, $batch_size);

            if (empty($orders)) {
                break;
            }

            // Update all orders to JSON-encode the fraud report
            foreach ($orders as $order) {
                if (empty($order->fraud_report) || false === ($report = unserialize($order->fraud_report))) {
                    continue;
                }

                $this->Record->where('id', '=', $order->id)
                    ->update('orders', ['fraud_report' => $this->Json->encode($report)]);
            }

            // Set the LIMIT for the next records
            $start = $batch_size * $i;
            $i++;
        }
    }

    /**
     * Retrieves a set of orders
     *
     * @see OrderPlugin::upgrade2_11_2
     * @param int $limit_start The start record
     * @param int $size The number of records to retrieve
     * @return array An array of orders with a fraud report
     */
    private function getOrders($limit_start, $size)
    {
        return $this->Record->select(['id', 'fraud_report'])
            ->from('orders')
            ->where('fraud_report', '!=', null)
            ->order(['id' => 'ASC'])
            ->limit($size, $limit_start)
            ->fetchAll();
    }

    /**
     * Performs any necessary cleanup actions
     *
     * @param int $plugin_id The ID of the plugin being uninstalled
     * @param bool $last_instance True if $plugin_id is the last instance across
     *  all companies for this plugin, false otherwise
     */
    public function uninstall($plugin_id, $last_instance)
    {
        Loader::loadModels($this, ['CronTasks', 'EmailGroups', 'Emails', 'Permissions']);
        Configure::load('order', dirname(__FILE__) . DS . 'config' . DS);

        $emails = Configure::get('Order.install.emails');

        // Remove emails and email groups as necessary
        foreach ($emails as $email) {
            // Fetch the email template created by this plugin
            $group = $this->EmailGroups->getByAction($email['action']);

            // Delete all emails templates belonging to this plugin's email group and company
            if ($group) {
                $this->Emails->deleteAll($group->id, Configure::get('Blesta.company_id'));

                if ($last_instance) {
                    $this->EmailGroups->delete($group->id);
                }
            }
        }

        $permission = $this->Permissions->getByAlias('order.admin_forms', $plugin_id);
        if ($permission) {
            $this->Permissions->delete($permission->id);
        }

        $permission = $this->Permissions->getByAlias('order.admin_main', $plugin_id);
        if ($permission) {
            $this->Permissions->delete($permission->id);
        }

        $cron_task_run = $this->CronTasks->getTaskRunByKey('accept_paid_orders', 'order', false, 'plugin');

        if ($last_instance) {
            try {
                $this->Record->drop('order_forms');
                $this->Record->drop('order_form_groups');
                $this->Record->drop('order_form_meta');
                $this->Record->drop('order_form_currencies');
                $this->Record->drop('order_form_gateways');
                $this->Record->drop('order_staff_settings');
                $this->Record->drop('order_settings');
                $this->Record->drop('orders');
                $this->Record->drop('order_services');
            } catch (Exception $e) {
                // Error dropping... no permission?
                $this->Input->setErrors(['db' => ['create' => $e->getMessage()]]);
                return;
            }

            // Remove the cron task altogether
            $cron_task = $this->CronTasks->getByKey('accept_paid_orders', 'order', 'plugin');
            if ($cron_task) {
                $this->CronTasks->deleteTask($cron_task->id, 'plugin', 'order');
            }
        }

        // Remove individual task run
        if ($cron_task_run) {
            $this->CronTasks->deleteTaskRun($cron_task_run->task_run_id);
        }
    }

    /**
     * Returns all actions to be configured for this widget (invoked after install()
     * or upgrade(), overwrites all existing actions)
     *
     * @return array A numerically indexed array containing:
     *  - action The action to register for
     *  - uri The URI to be invoked for the given action
     *  - name The name to represent the action (can be language definition)
     */
    public function getActions()
    {
        return [
            [
                'action' => 'widget_staff_billing',
                'uri' => 'widget/order/admin_main/',
                'name' => 'OrderPlugin.admin_main.name'
            ],
            [
                'action' => 'nav_secondary_staff',
                'uri' => 'plugin/order/admin_forms/',
                'name' => 'OrderPlugin.admin_forms.name',
                'options' => ['parent' => 'packages/']
            ],
            [
                'action' => 'nav_primary_client',
                'uri' => 'order/',
                'name' => 'OrderPlugin.client.name',
                'options' => ['base_uri' => 'public']
            ]
        ];
    }

    /**
     * Returns the search options to append to the list of staff search options
     *
     * @param EventObject $event The event to process
     */
    public function getSearchOptions($event)
    {
        $params = $event->getParams();

        if (isset($params['options'])) {
            $params['options'] += [
                $params['base_uri'] . 'plugin/order/admin_main/search/'
                => Language::_('OrderPlugin.event_getsearchoptions.orders', true)
            ];
        }

        $event->setParams($params);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            [
                'event' => 'Clients.delete',
                'callback' => ['this', 'deleteHangingOrders']
            ],
            [
                'event' => 'AppController.structure',
                'callback' => ['this', 'setEmbedCode']
            ],
            [
                'event' => 'Navigation.getSearchOptions',
                'callback' => ['this', 'getSearchOptions']
            ],
        ];
    }

    /**
     * Deletes all order data that is no longer associated with a valid service or invoice
     *
     * @param EventObject $event The event to process
     */
    public function deleteHangingOrders($event)
    {
        Loader::loadModels($this, ['Order.OrderOrders']);

        $this->OrderOrders->deleteHangingOrders();
    }

    /**
     * Sets the defined embed code into the footer for order pages
     *
     * @param EventObject $event The event object representing the AppController.structure event
     */
    public function setEmbedCode(EventObject $event)
    {
        // Determine the action we're on and that it's in our white-list for us to set the embed code to this page
        $actions = [
            'signup/index',
            'main/index',
            'main/packages',
            'forms/index',
            'config/index',
            'config/preconfig',
            'checkout/index',
            'checkout/complete',
            'cart/index'
        ];

        $params = $event->getParams();
        $plugin = (!empty($params['plugin']) ? strtolower($params['plugin']) : null);
        $action = strtolower($params['controller'] . '/' . ($params['action'] == '' ? 'index' : $params['action']));
        if ($plugin !== 'order' || $params['portal'] != 'client' || !in_array($action, $actions)) {
            return;
        }

        // Fetch the embed code
        Loader::loadModels($this, ['Order.OrderSettings']);
        $embed_code = $this->OrderSettings->getSetting(Configure::get('Blesta.company_id'), 'embed_code');

        // If the embed code is not set, return
        if (empty($embed_code) || empty($embed_code->value)) {
            return;
        }

        Loader::loadHelpers($this, ['CurrencyFormat']);
        $this->CurrencyFormat->setCompany(Configure::get('Blesta.company_id'));

        // Load the template parser and generate the output
        $parser = new H2o();
        $parser->addFilter('currency_format', [$this->CurrencyFormat, 'format']);
        $parser_options = ['autoescape' => false];

        $output = $parser->parseString($embed_code->value, $parser_options)
            ->render($this->getEmbedCodeData($action, (!empty($params['get']) ? (array)$params['get'] : [])));

        // Update the event to set the embed code to just before the end body tag
        $value = $event->getReturnVal();
        $value['body_end'][] = $output;
        $event->setReturnVal($value);
    }

    /**
     * Retrieves the tag replacement data based on order information from the user's cart
     *
     * @param string The page controller/action being accessed
     * @param array $get The GET arguments
     * @return array An array of key/value pairs representing tag replacement data for embed codes
     */
    private function getEmbedCodeData($action, array $get)
    {
        Loader::loadComponents($this, ['Session']);

        // Load the SessionCart to retrieve the data tags to make available to the embed code
        // $get[0] is always presumed to be the order label referencing the cart session
        $cart_name = Configure::get('Blesta.company_id') . '-' . (empty($get[0]) ? '' : $get[0]);
        Loader::loadComponents($this, ['SessionCart' => [$cart_name, $this->Session]]);

        $data = [
            'order_page' => $action
        ];

        switch ($action) {
            case 'checkout/complete':
                // If checkout is complete, we have an order number at the 1st GET index
                Loader::loadModels($this, ['Order.OrderOrders', 'Invoices']);

                if (isset($get[1]) && ($order = $this->OrderOrders->getByNumber($get[1]))) {
                    $data['order'] = $order;
                    $data['invoice'] = (!empty($order->invoice_id)
                        ? $this->Invoices->get($order->invoice_id)
                        : null
                    );
                }
                break;
            default:
                // Do nothing
                break;
        }

        // Include currency and item information if available
        $cart = $this->SessionCart->get();

        // Include the currency from the invoice created, or fallback to the cart
        if (isset($data['invoice']) && !empty($data['invoice']->currency)) {
            $data['currency'] = $data['invoice']->currency;
        } elseif (!empty($cart['currency'])) {
            $data['currency'] = $cart['currency'];
        }

        // Set all item packages and package groups from the invoice, or fallback to the cart
        if (isset($data['invoice']) && is_object($data['invoice'])) {
            $data['products'] = $this->buildInvoiceItems($data['invoice']);
        } elseif (!empty($cart['items'])) {
            $data['products'] = $this->buildItems($cart['items']);
        }

        return $data;
    }

    /**
     * Retrieves item information representing each primary service from the given invoice
     * @see OrderPlugin::buildItems
     *
     * @param stdClass $invoice An stdClass object representing the invoice and containing
     *  - line_items A set of invoice line items
     */
    private function buildInvoiceItems(stdClass $invoice)
    {
        Loader::loadModels($this, ['Services']);
        $service_ids = [];

        // Retrieve the necessary invoice line item information to construct the items
        foreach ($invoice->line_items as $line_item) {
            // Skip line items that reference a service we've already seen in this loop
            // We care not for config options--only the primary service(s)
            if ($line_item->service_id === null || array_key_exists($line_item->service_id, $service_ids)) {
                continue;
            }

            if (($service = $this->Services->get($line_item->service_id))) {
                $service_ids[$service->id] = [
                    'pricing_id' => $service->pricing_id,
                    'group_id' => $service->package_group_id
                ];
            }
        }

        return $this->buildItems($service_ids);
    }

    /**
     * Creates an array of information representing each item
     *
     * @param array $items An array containing the:
     *  - pricing_id The ID of the selected pricing
     *  - group_id The ID of the package group
     */
    private function buildItems(array $items)
    {
        Loader::loadModels($this, ['Packages', 'PackageGroups']);

        $data = [];
        $packages = [];
        $package_groups = [];

        foreach ($items as $item) {
            // Skip items that are missing a pricing ID or package group ID,
            // or that has already been set (e.g. a config option)
            if (!isset($item['pricing_id']) || !isset($item['group_id']) || isset($packages[$item['pricing_id']])) {
                continue;
            }

            $product = (object)[];

            // Store the package in case it is used by additional items
            if (!isset($packages[$item['pricing_id']])) {
                $packages[$item['pricing_id']] = $this->Packages->getByPricingId($item['pricing_id']);
            }

            // Set the package for this item
            if (!empty($packages[$item['pricing_id']])) {
                $product->package = $packages[$item['pricing_id']];
            }

            // Store the package group in case it is used by additional items
            if (!isset($packages[$item['group_id']])) {
                $package_groups[$item['group_id']] = $this->PackageGroups->get($item['group_id']);
            }

            // Set the package group for this item
            if (!empty($package_groups[$item['group_id']])) {
                $product->package_group = $package_groups[$item['group_id']];
            }

            $data[] = $product;
        }

        return $data;
    }

    /**
     * Execute the cron task
     *
     * @param string $key The cron task to execute
     */
    public function cron($key)
    {
        if ($key == 'accept_paid_orders') {
            Loader::loadModels($this, ['Order.OrderOrders']);
            $this->OrderOrders->acceptPaidOrders();
        }
    }
}
