<?php
/**
 * Support Manager plugin handler
 *
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerPlugin extends Plugin
{
    public function __construct()
    {
        Language::loadLang('support_manager_plugin', null, dirname(__FILE__) . DS . 'language' . DS);

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
        if (!isset($this->Record)) {
            Loader::loadComponents($this, ['Record']);
        }
        Loader::loadModels($this, ['CronTasks', 'Emails', 'EmailGroups', 'Languages', 'Permissions']);

        Configure::load('support_manager', dirname(__FILE__) . DS . 'config' . DS);

        // Add all support tables, *IFF* not already added
        try {
            // Tickets
            $this->Record
                ->setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])
                ->setField('code', ['type'=>'int', 'unsigned'=>true, 'size'=>10])
                ->setField('department_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])
                ->setField('staff_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null])
                ->setField(
                    'service_id',
                    ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null]
                )
                ->setField('client_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null])
                ->setField('email', ['type'=>'varchar', 'size'=>255, 'is_null'=>true, 'default'=>null])
                ->setField('summary', ['type'=>'varchar', 'size'=>255])
                ->setField(
                    'priority',
                    ['type'=>'enum', 'size'=>"'emergency','critical','high','medium','low'", 'default'=>'low']
                )
                ->setField(
                    'status',
                    [
                        'type'=>'enum',
                        'size'=>"'open','awaiting_reply','in_progress','on_hold','closed','trash'",
                        'default'=>'open'
                    ]
                )
                ->setField('date_added', ['type'=>'datetime'])
                ->setField('date_updated', ['type'=>'datetime'])
                ->setField('date_closed', ['type'=>'datetime', 'is_null'=>true, 'default'=>null])
                ->setKey(['id'], 'primary')
                ->setKey(['code'], 'index')
                ->setKey(['date_added', 'status'], 'index')
                ->setKey(['department_id', 'status'], 'index')
                ->create('support_tickets', true);

            // Replies
            $this->Record
                ->setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])
                ->setField('ticket_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])
                ->setField('staff_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null])
                ->setField(
                    'contact_id',
                    ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null]
                )
                ->setField('type', ['type'=>'enum', 'size'=>"'reply','note','log'", 'default'=>'reply'])
                ->setField('details', ['type'=>'mediumtext'])
                ->setField('date_added', ['type'=>'datetime'])
                ->setKey(['id'], 'primary')
                ->setKey(['ticket_id', 'type'], 'index')
                ->create('support_replies', true);

            // Attachments
            $this->Record->
                setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])->
                setField('reply_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('name', ['type'=>'varchar', 'size'=>255])->
                setField('file_name', ['type'=>'varchar', 'size'=>255])->
                setKey(['id'], 'primary')->
                setKey(['reply_id'], 'index')->
                create('support_attachments', true);

            // Departments
            $this->Record
                ->setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])
                ->setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])
                ->setField('name', ['type'=>'varchar', 'size'=>255])
                ->setField('description', ['type'=>'text'])
                ->setField('email', ['type'=>'varchar', 'size'=>255])
                ->setField('method', ['type'=>'enum', 'size'=>"'pipe','pop3','imap','none'", 'default'=>'pipe'])
                ->setField(
                    'default_priority',
                    ['type'=>'enum', 'size'=>"'emergency','critical','high','medium','low'", 'default'=>'low']
                )
                ->setField('host', ['type'=>'varchar', 'size'=>128, 'is_null' => true, 'default' => null])
                ->setField('user', ['type'=>'varchar', 'size'=>64, 'is_null' => true, 'default' => null])
                ->setField('pass', ['type'=>'text', 'is_null' => true, 'default' => null])
                ->setField('port', ['type'=>'smallint', 'size'=>6, 'is_null' => true, 'default' => null])
                ->setField(
                    'security',
                    ['type'=>'enum', 'size'=>"'none','ssl','tls'", 'is_null' => true, 'default' => null]
                )
                ->setField('box_name', ['type'=>'varchar', 'size'=>255, 'is_null' => true, 'default' => null])
                ->setField(
                    'mark_messages',
                    ['type'=>'enum', 'size'=>"'read','deleted'", 'is_null' => true, 'default' => null]
                )
                ->setField('clients_only', ['type'=>'tinyint', 'size'=>1, 'default'=>1])
                ->setField('override_from_email', ['type'=>'tinyint', 'size'=>1, 'default'=>1])
                ->setField('send_ticket_received', ['type'=>'tinyint', 'size'=>1, 'default'=>1])
                ->setField('automatic_transition', ['type'=>'tinyint', 'size'=>1, 'default'=>0])
                ->setField('close_ticket_interval', ['type'=>'int', 'size'=>10, 'is_null'=>true, 'default'=>null])
                ->setField('delete_ticket_interval', ['type'=>'int', 'size'=>10, 'is_null'=>true, 'default'=>null])
                ->setField('response_id', ['type'=>'int', 'size'=>10, 'is_null'=>true, 'default'=>null])
                ->setField('status', ['type'=>'enum', 'size'=>"'hidden','visible'", 'default'=>'visible'])
                ->setKey(['id'], 'primary')
                ->setKey(['company_id'], 'index')
                ->setKey(['status', 'company_id'], 'index')
                ->create('support_departments', true);

            // Staff Departments
            $this->Record->
                setField('department_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('staff_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setKey(['department_id', 'staff_id'], 'primary')->
                create('support_staff_departments', true);

            // Staff Schedules
            $this->Record->
                setField('staff_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('day', ['type'=>'enum', 'size'=>"'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'"])->
                setField('start_time', ['type'=>'time'])->
                setField('end_time', ['type'=>'time'])->
                setKey(['staff_id', 'company_id', 'day'], 'primary')->
                create('support_staff_schedules', true);

            // Response Categories
            $this->Record
                ->setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])
                ->setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])
                ->setField(
                    'parent_id',
                    ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null' => true, 'default' => null]
                )
                ->setField('name', ['type'=>'varchar', 'size'=>64])
                ->setKey(['id'], 'primary')
                ->setKey(['company_id'], 'index')
                ->setKey(['parent_id', 'company_id'], 'index')
                ->create('support_response_categories', true);

            // Responses
            $this->Record->
                setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])->
                setField('category_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('name', ['type'=>'varchar', 'size'=>64])->
                setField('details', ['type'=>'mediumtext'])->
                setKey(['id'], 'primary')->
                setKey(['category_id'], 'index')->
                create('support_responses', true);

            // Settings
            $this->Record->
                setField('key', ['type'=>'varchar', 'size'=>32])->
                setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('value', ['type'=>'text'])->
                setKey(['key', 'company_id'], 'primary')->
                create('support_settings', true);

            // Staff Settings
            $this->Record->
                setField('key', ['type'=>'varchar', 'size'=>32])->
                setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('staff_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('value', ['type'=>'text'])->
                setKey(['key', 'company_id', 'staff_id'], 'primary')->
                create('support_staff_settings', true);

            // Knowledge base articles
            $this->Record->
                setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])->
                setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('access', ['type'=>'enum', 'size'=>"'public','private','hidden'", 'default'=>'public'])->
                setField('up_votes', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'default'=>0])->
                setField('down_votes', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'default'=>0])->
                setField('date_created', ['type'=>'datetime'])->
                setField('date_updated', ['type'=>'datetime'])->
                setKey(['id'], 'primary')->
                setKey(['company_id', 'access'], 'index')->
                create('support_kb_articles', true);

            // Knowledge base article categories
            $this->Record->
                setField('category_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('article_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setKey(['category_id', 'article_id'], 'primary')->
                create('support_kb_article_categories', true);

            // Knowledgebase article content
            $this->Record->
                setField('article_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('lang', ['type'=>'char', 'size'=>5])->
                setField('title', ['type'=>'varchar', 'size'=>255])->
                setField('body', ['type'=>'mediumtext'])->
                setField('content_type', ['type'=>'enum', 'size'=>"'text','html'", 'default'=>'text'])->
                setKey(['article_id', 'lang'], 'primary')->
                create('support_kb_article_content', true);

            // Knowledgebase categories
            $this->Record->
                setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])->
                setField('parent_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null])->
                setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                setField('name', ['type'=>'varchar', 'size'=>255])->
                setField('description', ['type'=>'text', 'is_null'=>true, 'default'=>null])->
                setField('access', ['type'=>'enum', 'size'=>"'public','private','hidden'", 'default'=>'public'])->
                setField('date_created', ['type'=>'datetime'])->
                setField('date_updated', ['type'=>'datetime'])->
                setKey(['id'], 'primary')->
                setKey(['company_id', 'parent_id', 'access'], 'index')->
                create('support_kb_categories', true);

            // Set the uploads directory
            Loader::loadComponents($this, ['SettingsCollection', 'Upload']);
            $temp = $this->SettingsCollection->fetchSetting(null, Configure::get('Blesta.company_id'), 'uploads_dir');
            $upload_path = $temp['value'] . Configure::get('Blesta.company_id') . DS . 'support_manager_files' . DS;
            // Create the upload path if it doesn't already exist
            $this->Upload->createUploadPath($upload_path, 0777);
        } catch (Exception $e) {
            // Error adding... no permission?
            $this->Input->setErrors(['db'=> ['create'=>$e->getMessage()]]);
            return;
        }

        // Add cron tasks
        $this->addCronTasks($this->getCronTasks());

        // Fetch all currently-installed languages for this company, for which email templates should be created for
        $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

        // Add all email templates
        $emails = Configure::get('SupportManager.install.emails');
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

        // Add ACL permissions
        $permissions = Configure::get('SupportManager.install.permissions');
        foreach ($permissions as $set) {
            $group_id = $this->Permissions->addGroup([
                'plugin_id' => $plugin_id,
                'name' => Language::_($set['name'], true),
                'level' => $set['level'],
                'alias' => $set['alias']
            ]);

            foreach ($set['permissions'] as $permission) {
                $this->Permissions->add([
                    'group_id' => $group_id,
                    'plugin_id' => $plugin_id,
                    'name' => Language::_($permission['name'], true),
                    'alias' => $permission['alias'],
                    'action' => $permission['action']
                ]);
            }
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
        Configure::load('support_manager', dirname(__FILE__) . DS . 'config' . DS);

        // Upgrade if possible
        if (version_compare($this->getVersion(), $current_version, '>')) {
            // Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered

            // Upgrade to 1.1.0
            if (version_compare($current_version, '1.1.0', '<')) {
                // Add ACL permission for client widget
                Loader::loadModels($this, ['Permissions']);

                Configure::load('support_manager', dirname(__FILE__) . DS . 'config' . DS);
                $permissions = Configure::get('SupportManager.install.permissions');
                $plugin_permission = $this->Permissions->getByAlias('support_manager.admin_tickets', $plugin_id);

                // Add the new client widget permission available since v1.1.0
                if ($plugin_permission && $permissions) {
                    foreach ($permissions as $set) {
                        if ($set['name'] == 'SupportManagerPlugin.permission.admin_main') {
                            foreach ($set['permissions'] as $permission) {
                                if ($permission['name'] == 'SupportManagerPlugin.permission.admin_tickets_client') {
                                    $this->Permissions->add([
                                        'group_id' => $plugin_permission->group_id,
                                        'plugin_id' => $plugin_id,
                                        'name' => Language::_($permission['name'], true),
                                        'alias' => $permission['alias'],
                                        'action' => $permission['action']
                                    ]);
                                    if (($errors = $this->Permissions->errors())) {
                                        $this->Input->setErrors($errors);
                                    }
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }

            // Upgrade to 1.2.0
            if (version_compare($current_version, '1.2.0', '<')) {
                // Update format of existing staff settings
                $settings_stmt = $this->Record->select()->from('support_staff_settings')->
                    open()->
                        where('key', '=', 'mobile_ticket_emails')->
                        orWhere('key', '=', 'ticket_emails')->
                    close()->
                    getStatement();

                // Fetch the department priorities
                Loader::loadModels($this, ['SupportManager.SupportManagerDepartments']);
                $priorities = $this->SupportManagerDepartments->getPriorities();

                // Set default setting values to true (i.e. to receive ticket emails)
                $values = [];
                foreach ($priorities as $key => $language) {
                    $values[$key] = 'true';
                }

                // Begin a transaction
                $this->Record->begin();

                // Update each setting
                while (($setting = $settings_stmt->fetch())) {
                    // Build the new setting
                    $new_setting = (array)$setting;
                    $new_setting['value'] = $values;

                    // Set values to false
                    if ($setting->value == 'false') {
                        foreach ($new_setting['value'] as &$value) {
                            $value = 'false';
                        }
                    }

                    // Update the setting
                    $new_setting['value'] = serialize($new_setting['value']);
                    $this->Record->duplicate('value', '=', $new_setting['value'])->
                        insert('support_staff_settings', $new_setting);
                }

                // Commit the transaction
                $this->Record->commit();
            }

            // Upgrade to 1.5.0
            if (version_compare($current_version, '1.5.0', '<')) {
                // Update email template tags to include {ticket.summary} and {ticket_hash_code}
                $this->Record->begin();

                $vars = ['tags' => '{ticket},{ticket.summary},{ticket_hash_code}'];
                $this->Record->where('action', '=', 'SupportManager.ticket_received')
                    ->update('email_groups', $vars);
                $this->Record->where('action', '=', 'SupportManager.staff_ticket_updated')
                    ->update('email_groups', $vars);
                $this->Record->where('action', '=', 'SupportManager.staff_ticket_updated_mobile')
                    ->update('email_groups', $vars);

                $vars = ['tags' => '{ticket},{ticket.summary},{update_ticket_url},{ticket_hash_code}'];
                $this->Record->where('action', '=', 'SupportManager.ticket_updated')
                    ->update('email_groups', $vars);

                $this->Record->commit();
            }

            // Upgrade to 1.5.2
            if (version_compare($current_version, '1.5.2', '<')) {
                Loader::loadModels($this, ['Emails', 'EmailGroups', 'Languages']);

                // Add emails missing in additional languages that have been installed before the plugin was installed
                $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

                // Add all email templates in other languages IFF they do not alreday exist
                $emails = Configure::get('SupportManager.install.emails');
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

            // Upgrade to 1.5.3
            if (version_compare($current_version, '1.5.3', '<')) {
                // Fetch all client/staff ticket updated emails to remove the http protocol from mailto links
                $email_group_action = ['SupportManager.ticket_updated', 'SupportManager.staff_ticket_updated'];

                // Remove http from mailto links
                foreach ($email_group_action as $action) {
                    // Fetch all ticket emails
                    $emails = $this->Record->select(['emails.id', 'emails.text', 'emails.html'])->from('emails')->
                        on('email_groups.id', '=', 'emails.email_group_id', false)->
                        innerJoin('email_groups', 'email_groups.action', '=', $action)->
                        getStatement();

                    // Update each ticket updated email to remove the HTTP protocol from mailto links
                    foreach ($emails as $email) {
                        // Update HTML to fix mailto for Ticket Updated and Staff Ticket Updated templates
                        $html_replace = ['http://mailto:'];
                        $html_replace_with = ['mailto:'];

                        // Set HTTP protocol on Ticket Updated email only
                        if ($action == 'SupportManager.ticket_updated') {
                            $html_replace[] = '<a href="http://{update_ticket_url}">{update_ticket_url}</a>';
                            $html_replace_with[]
                                = '<a href="http://{update_ticket_url}">http://{update_ticket_url}</a>';
                        }

                        $vars = [
                            // Set HTTP protocol for text on Ticket Updated email only
                            'text' => ($action == 'SupportManager.ticket_updated'
                                ? str_replace('{update_ticket_url}', 'http://{update_ticket_url}', $email->text)
                                : $email->text
                            ),
                            'html' => str_replace($html_replace, $html_replace_with, $email->html)
                        ];

                        if ($vars['html'] != $email->html || $vars['text'] != $email->text) {
                            $this->Record->where('id', '=', $email->id)->update('emails', $vars);
                        }
                    }
                }
            }

            // Upgrade to 1.6.0
            if (version_compare($current_version, '1.6.0', '<')) {
                // Update support departments to include the new override_from_email field
                $this->Record->query(
                    "ALTER TABLE `support_departments`
                    ADD `override_from_email` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `clients_only` ;"
                );
            }

            // Upgrade to 1.6.4
            if (version_compare($current_version, '1.6.4', '<')) {
                Loader::loadModels($this, ['SupportManager.SupportManagerDepartments']);

                // Set date_closed for tickets that are closed
                $tickets = $this->Record->select(['id'])->from('support_tickets')->
                    where('status', '=', 'closed')->where('date_closed', '=', null)->
                    getStatement();

                // Set closed date to now
                foreach ($tickets as $ticket) {
                    $this->Record
                        ->where('id', '=', $ticket->id)
                        ->update(
                            'support_tickets',
                            ['date_closed' => $this->SupportManagerDepartments->dateToUtc(date('c'))]
                        );
                }
            }

            // Upgrade to 2.0.0
            if (version_compare($current_version, '2.0.0', '<')) {
                Loader::loadModels($this, ['CronTasks']);

                // Fetch all client ticket updated emails to update default text content
                $emails = $this->Record->select(['emails.id', 'emails.text', 'emails.html'])->from('emails')->
                    on('email_groups.id', '=', 'emails.email_group_id', false)->
                    innerJoin('email_groups', 'email_groups.action', '=', 'SupportManager.ticket_updated')->
                    getStatement();

                // Update email templates
                foreach ($emails as $email) {
                    // Update text
                    $replace = 'If you are a client, you may also update the ticket in our support area at';
                    $replace_with = 'You may also update the ticket in our support area at';

                    $vars = [
                        'text' => str_replace($replace, $replace_with, $email->text),
                        'html' => str_replace($replace, $replace_with, $email->html)
                    ];

                    if ($vars['html'] != $email->html || $vars['text'] != $email->text) {
                        $this->Record->where('id', '=', $email->id)->update('emails', $vars);
                    }
                }

                // Add new cron task to auto-close open tickets
                $cron_tasks = $this->getCronTasks();
                $task = null;
                foreach ($cron_tasks as $task) {
                    if ($task['key'] == 'close_tickets') {
                        break;
                    }
                }

                if ($task) {
                    $this->addCronTasks([$task]);
                }

                // Update support departments to include the new close_ticket_interval field
                $this->Record->query(
                    'ALTER TABLE `support_departments`
                    ADD `close_ticket_interval` INT( 10 ) NULL DEFAULT NULL AFTER `override_from_email` ;'
                );
                $this->Record->query(
                    'ALTER TABLE `support_departments`
                    ADD `response_id` INT( 10 ) NULL DEFAULT NULL AFTER `close_ticket_interval` ;'
                );

                // Add new index
                $this->Record->query('ALTER TABLE `support_tickets` ADD INDEX ( `department_id` , `status` ) ;');
            }

            // Upgrade to 2.4.0
            if (version_compare($current_version, '2.4.0', '<')) {
                // Add new database tables for the knowledge base
                $this->Record->
                    setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])->
                    setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                    setField('access', ['type'=>'enum', 'size'=>"'public','private','hidden'", 'default'=>'public'])->
                    setField('up_votes', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'default'=>0])->
                    setField('down_votes', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'default'=>0])->
                    setField('date_created', ['type'=>'datetime'])->
                    setField('date_updated', ['type'=>'datetime'])->
                    setKey(['id'], 'primary')->
                    setKey(['company_id', 'access'], 'index')->
                    create('support_kb_articles', true);
                $this->Record->
                    setField('category_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                    setField('article_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                    setKey(['category_id', 'article_id'], 'primary')->
                    create('support_kb_article_categories', true);
                $this->Record->
                    setField('article_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])->
                    setField('lang', ['type'=>'char', 'size'=>5])->
                    setField('title', ['type'=>'varchar', 'size'=>255])->
                    setField('body', ['type'=>'mediumtext'])->
                    setField('content_type', ['type'=>'enum', 'size'=>"'text','html'", 'default'=>'text'])->
                    setKey(['article_id', 'lang'], 'primary')->
                    create('support_kb_article_content', true);
                $this->Record
                    ->setField('id', ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'auto_increment'=>true])
                    ->setField(
                        'parent_id',
                        ['type'=>'int', 'size'=>10, 'unsigned'=>true, 'is_null'=>true, 'default'=>null]
                    )
                    ->setField('company_id', ['type'=>'int', 'size'=>10, 'unsigned'=>true])
                    ->setField('name', ['type'=>'varchar', 'size'=>255])
                    ->setField('description', ['type'=>'text', 'is_null'=>true, 'default'=>null])
                    ->setField('access', ['type'=>'enum', 'size'=>"'public','private','hidden'", 'default'=>'public'])
                    ->setField('date_created', ['type'=>'datetime'])
                    ->setField('date_updated', ['type'=>'datetime'])
                    ->setKey(['id'], 'primary')
                    ->setKey(['company_id', 'parent_id', 'access'], 'index')
                    ->create('support_kb_categories', true);

                // Add ACL permission for the knowledgebase
                Loader::loadModels($this, ['Permissions']);
                $permissions = Configure::get('SupportManager.install.permissions');
                $plugin_permission = $this->Permissions->getByAlias('support_manager.admin_tickets', $plugin_id);

                // Add the new knowledgebase permissions
                if ($plugin_permission && $permissions) {
                    foreach ($permissions as $set) {
                        if ($set['name'] == 'SupportManagerPlugin.permission.admin_main') {
                            foreach ($set['permissions'] as $permission) {
                                if ($permission['name'] == 'SupportManagerPlugin.permission.admin_knowledgebase') {
                                    $this->Permissions->add([
                                        'group_id' => $plugin_permission->group_id,
                                        'plugin_id' => $plugin_id,
                                        'name' => Language::_($permission['name'], true),
                                        'alias' => $permission['alias'],
                                        'action' => $permission['action']
                                    ]);
                                    if (($errors = $this->Permissions->errors())) {
                                        $this->Input->setErrors($errors);
                                    }
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }

            // Upgrade to v2.5.0
            if (version_compare($current_version, '2.5.0', '<')) {
                // Add a field for contacts that reply to tickets
                $this->Record->query(
                    'ALTER TABLE `support_replies`
                    ADD `contact_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `staff_id`;'
                );
            }

            // Upgrade to v2.6.0
            if (version_compare($current_version, '2.6.0', '<')) {
                // Add a new email template for when staff are assigned to a ticket
                Loader::loadModels($this, ['Emails', 'EmailGroups', 'Languages']);
                $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

                $emails = Configure::get('SupportManager.install.emails');
                foreach ($emails as $email) {
                    // Only add this one ticket assigned template if it doesn't already exist
                    if ($email['action'] != 'SupportManager.staff_ticket_assigned'
                        || ($group = $this->EmailGroups->getByAction($email['action']))
                    ) {
                        continue;
                    }

                    $group_id = $this->EmailGroups->add([
                        'action' => $email['action'],
                        'type' => $email['type'],
                        'plugin_dir' => $email['plugin_dir'],
                        'tags' => $email['tags']
                    ]);

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
            }

            // Upgrade to v2.12.0
            if (version_compare($current_version, '2.12.0', '<')) {
                if (!isset($this->SupportManagerDepartments)) {
                    Loader::loadModels($this, ['SupportManager.SupportManagerDepartments']);
                }

                // Add a field for contacts that reply to tickets
                $this->Record->query('ALTER TABLE `support_departments` CHANGE `pass` `pass` TEXT NULL DEFAULT NULL;');

                $departments = $this->Record->select()->from('support_departments')->fetchAll();

                // Encrypt all department passwords
                foreach ($departments as $department) {
                    $this->Record->where('id', '=', $department->id)
                        ->update(
                            'support_departments',
                            ['pass' => $this->SupportManagerDepartments->systemEncrypt($department->pass)],
                            ['pass']
                        );
                }

                $this->Record->query(
                    "ALTER TABLE `support_departments`
                    ADD `send_ticket_received` TINYINT(1) NOT NULL DEFAULT '1' AFTER `override_from_email`;"
                );
            }

            // Upgrade to v2.14.0
            if (version_compare($current_version, '2.14.0', '<')) {
                Loader::loadModels($this, ['CronTasks']);

                // Add the 'trash' status
                $this->Record->query("ALTER TABLE `support_tickets`
                    CHANGE `status` `status` ENUM ('open','awaiting_reply','in_progress','closed','trash')
                    NOT NULL DEFAULT 'open';
                ");

                // Add a 'date_updated' column to the 'support_tickets' table
                $this->Record->query('ALTER TABLE `support_tickets` ADD `date_updated` DATETIME NULL DEFAULT NULL
                    AFTER `date_added`;
                ');

                // Add a 'delete_ticket_interval' column to the 'support_departments' table
                $this->Record->query('ALTER TABLE `support_departments`
                    ADD `delete_ticket_interval` INT(10) NULL DEFAULT NULL AFTER `close_ticket_interval`;
                ');

                // Set date_updated to date_added for all tickets
                $this->Record->set('support_tickets.date_updated', 'support_tickets.date_added', false)
                    ->update('support_tickets');

                // Make support_tickets.date_updated not null by default
                $this->Record->query('ALTER TABLE `support_tickets`
                    CHANGE `date_updated` `date_updated` DATETIME NOT NULL;
                ');

                // Add ACL permission for the tickets
                Loader::loadModels($this, ['Permissions']);
                $plugin_permission = $this->Permissions->getByAlias('support_manager.admin_tickets', $plugin_id);

                // Add the new ticket permissions
                if ($plugin_permission) {
                    $this->Permissions->add([
                        'group_id' => $plugin_permission->group_id,
                        'plugin_id' => $plugin_id,
                        'name' => Language::_('SupportManagerPlugin.permission.admin_tickets_delete', true),
                        'alias' => 'suport_manager.admin_tickets',
                        'action' => 'delete'
                    ]);

                    if (($errors = $this->Permissions->errors())) {
                        $this->Input->setErrors($errors);
                    }
                }

                // Add new cron task to auto-delete trash tickets
                $cron_tasks = $this->getCronTasks();
                $task = null;
                foreach ($cron_tasks as $task) {
                    if ($task['key'] == 'delete_tickets') {
                        break;
                    }
                }

                if ($task) {
                    $this->addCronTasks([$task]);
                }
            }

            // Upgrade to v2.14.1
            if (version_compare($current_version, '2.14.1', '<')) {
                Loader::loadModels($this, ['StaffGroups', 'Permissions']);
                Loader::loadComponents($this, ['Acl']);

                $plugin_permission = $this->Permissions->getByAlias('support_manager.admin_tickets', $plugin_id);

                if ($plugin_permission) {
                    $staff_groups = $this->StaffGroups->getAll();

                    // Deny permission for ticket deletion to all staff groups by default
                    foreach ($staff_groups as $staff_group) {
                        $this->Acl->allow('staff_group_' . $staff_group->id, 'support_manager.admin_tickets', 'delete');
                    }
                }
            }

            // Upgrade to v2.15.0
            if (version_compare($current_version, '2.15.0', '<')) {
                // Add the 'on_hold' status
                $this->Record->query("ALTER TABLE `support_tickets`
                    CHANGE `status`
                    `status` ENUM ('open','awaiting_reply','in_progress','on_hold','closed','trash')
                    NOT NULL DEFAULT 'open';
                ");

                // Add index on client_id to the support_tickets table
                $this->Record->query('ALTER TABLE `support_tickets` ADD INDEX (`client_id`);');
            }

            // Upgrade to v2.16.0
            if (version_compare($current_version, '2.16.0', '<')) {
                Loader::loadModels($this, ['StaffGroups', 'Permissions']);
                Loader::loadComponents($this, ['Acl']);

                $misspelled_permission = $this->Permissions->getByAlias('suport_manager.admin_tickets', $plugin_id);

                if ($misspelled_permission) {
                    $this->Permissions->edit(
                        $misspelled_permission->id,
                        [
                            'group_id' => $misspelled_permission->group_id,
                            'name' => $misspelled_permission->name,
                            'alias' => 'support_manager.admin_tickets',
                            'action' => $misspelled_permission->action
                        ]
                    );
                }
            }

            // Upgrade to v2.16.2
            if (version_compare($current_version, '2.16.2', '<')) {
                // Add the {ticket_hash_code} to the ticket_received email templates that don't already have it
                $emails = $this->Record->select(['emails.id', 'emails.subject'])
                    ->from('email_groups')
                    ->innerJoin('emails', 'emails.email_group_id', '=', 'email_groups.id', false)
                    ->where('email_groups.action', '=', 'SupportManager.ticket_received')
                    ->notLike('emails.subject', '%{ticket_hash_code}%')
                    ->fetchAll();

                foreach ($emails as $email) {
                    $this->Record->where('emails.id', '=', $email->id)
                        ->update('emails', ['subject' => $email->subject . ' {ticket_hash_code}']);
                }
            }

            // Upgrade to v2.17.0
            if (version_compare($current_version, '2.17.0', '<')) {
                // Add a 'automatic_transition' column to the 'support_departments' table
                $this->Record->query('ALTER TABLE `support_departments` ADD `automatic_transition` TINYINT(1) NOT NULL
                    DEFAULT 0 AFTER `send_ticket_received`;
                ');
            }

            // Upgrade to 2.20.0
            if (version_compare($current_version, '2.20.0', '<')) {
                // Update email template tags to include {contact}
                $this->Record->query(
                    "UPDATE `email_groups` SET tags = CONCAT(`tags`, ',{client},{reply_contact}')
                    WHERE action IN (?, ?, ?)",
                    [
                        'SupportManager.ticket_received',
                        'SupportManager.staff_ticket_updated',
                        'SupportManager.staff_ticket_updated_mobile'
                    ]
                );
                $this->Record->query(
                    "UPDATE `email_groups` SET tags = CONCAT(`tags`, ',{client}') WHERE action = ?",
                    ['SupportManager.ticket_updated']
                );
            }
        }
    }

    /**
     * Performs any necessary cleanup actions
     *
     * @param int $plugin_id The ID of the plugin being uninstalled
     * @param bool $last_instance True if $plugin_id is the last instance
     *  across all companies for this plugin, false otherwise
     */
    public function uninstall($plugin_id, $last_instance)
    {
        Loader::loadModels($this, ['CronTasks', 'Emails', 'EmailGroups', 'Permissions']);
        Configure::load('support_manager', dirname(__FILE__) . DS . 'config' . DS);

        $permissions = Configure::get('SupportManager.install.permissions');
        $emails = Configure::get('SupportManager.install.emails');

        // Fetch the cron tasks for this plugin
        $cron_tasks = $this->getCronTasks();

        // Remove the tables created by this plugin
        if ($last_instance) {
            try {
                // Uninstall tables
                $this->Record->drop('support_tickets');
                $this->Record->drop('support_replies');
                $this->Record->drop('support_attachments');
                $this->Record->drop('support_departments');
                $this->Record->drop('support_staff_departments');
                $this->Record->drop('support_staff_schedules');
                $this->Record->drop('support_response_categories');
                $this->Record->drop('support_responses');
                $this->Record->drop('support_settings');
                $this->Record->drop('support_staff_settings');
                $this->Record->drop('support_kb_articles');
                $this->Record->drop('support_kb_article_categories');
                $this->Record->drop('support_kb_article_content');
                $this->Record->drop('support_kb_categories');
            } catch (Exception $e) {
                // Error dropping... no permission?
                $this->Input->setErrors(['db'=> ['create'=>$e->getMessage()]]);
                return;
            }

            // Remove permission groups
            foreach ($permissions as $set) {
                $group = $this->Permissions->getGroupByAlias($set['alias'], $plugin_id);

                if ($group) {
                    $this->Permissions->deleteGroup($group->id);
                }
            }

            // Remove the cron tasks
            foreach ($cron_tasks as $task) {
                $cron_task = $this->CronTasks->getByKey($task['key'], $task['dir'], $task['task_type']);
                if ($cron_task) {
                    $this->CronTasks->deleteTask($cron_task->id, $task['task_type'], $task['dir']);
                }
            }
        }

        // Remove individual cron task runs
        foreach ($cron_tasks as $task) {
            $cron_task_run = $this->CronTasks->getTaskRunByKey($task['key'], $task['dir'], false, $task['task_type']);
            if ($cron_task_run) {
                $this->CronTasks->deleteTaskRun($cron_task_run->task_run_id);
            }
        }

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

        // Remove permissions
        if (!$last_instance) {
            foreach ($permissions as $set) {
                foreach ($set['permissions'] as $permission) {
                    $permission = $this->Permissions->getByAlias($permission['alias'], $plugin_id);

                    if ($permission) {
                        $this->Permissions->delete($permission->id);
                    }
                }

                // Get the permission group
                $group = $this->Permissions->getGroupByAlias($set['alias'], $plugin_id);

                if ($group) {
                    $this->Permissions->deleteGroup($group->id);
                }
            }
        }
    }

    /**
     * Returns all actions to be configured for this widget
     * (invoked after install() or upgrade(), overwrites all existing actions)
     *
     * @return array A numerically indexed array containing:
     *  - action The action to register for
     *  - uri The URI to be invoked for the given action
     *  - name The name to represent the action (can be language definition)
     *  - options An array of key/value pair options for the given action
     */
    public function getActions()
    {
        return [
            // Client Nav
            [
                'action' => 'nav_primary_client',
                'uri' => 'plugin/support_manager/client_main/',
                'name' => 'SupportManagerPlugin.nav_primary_client.main',
                'options' => [
                    'sub' => [
                        [
                            'uri' => 'plugin/support_manager/client_tickets/',
                            'name' => 'SupportManagerPlugin.nav_primary_client.tickets'
                        ],
                        [
                            'uri' => 'plugin/support_manager/knowledgebase/',
                            'name' => 'SupportManagerPlugin.nav_primary_client.knowledgebase'
                        ]
                    ]
                ]
            ],
            // Staff Nav
            [
                'action' => 'nav_primary_staff',
                'uri' => 'plugin/support_manager/admin_main/',
                'name' => 'SupportManagerPlugin.nav_primary_staff.main',
                'options' => [
                    'sub' => [
                        [
                            'uri' => 'plugin/support_manager/admin_tickets/',
                            'name' => 'SupportManagerPlugin.nav_primary_staff.tickets'
                        ],
                        [
                            'uri' => 'plugin/support_manager/admin_departments/',
                            'name' => 'SupportManagerPlugin.nav_primary_staff.departments'
                        ],
                        [
                            'uri' => 'plugin/support_manager/admin_responses/',
                            'name' => 'SupportManagerPlugin.nav_primary_staff.responses'
                        ],
                        [
                            'uri' => 'plugin/support_manager/admin_staff/',
                            'name' => 'SupportManagerPlugin.nav_primary_staff.staff'
                        ],
                        [
                            'uri' => 'plugin/support_manager/admin_knowledgebase/',
                            'name' => 'SupportManagerPlugin.nav_primary_staff.knowledgebase'
                        ]
                    ]
                ]
            ],
            // Widget
            [
                'action' => 'widget_staff_client',
                'uri' => 'plugin/support_manager/admin_tickets/client/',
                'name' => 'SupportManagerPlugin.widget_staff_client.tickets'
            ],
            // Client Profile Action Link
            [
                'action' => 'action_staff_client',
                'uri' => 'plugin/support_manager/admin_tickets/add/',
                'name' => 'SupportManagerPlugin.action_staff_client.add',
                'options' => [
                    'class' => 'ticket'
                ]
            ]
        ];
    }

    /**
     * Returns all events to be registered for this plugin
     * (invoked after install() or upgrade(), overwrites all existing events)
     *
     * @return array A numerically indexed array containing:
     *  - event The event to register for
     *  - callback A string or array representing a callback function or class/method.
     *      If a user (e.g. non-native PHP) function or class/method, the plugin must
     *      automatically define it when the plugin is loaded. To invoke an instance
     *      methods pass "this" instead of the class name as the 1st callback element.
     */
    public function getEvents()
    {
        return [
            [
                'event' => 'Report.clientData',
                'callback' => ['this', 'getClientData']
            ],
            [
                'event' => 'Navigation.getSearchOptions',
                'callback' => ['this', 'getSearchOptions']
            ],
            [
                'event' => 'Clients.delete',
                'callback' => ['this', 'deleteClientTickets']
            ]
        ];
    }

    /**
     * Appends ticket information to the existing client data
     *
     * @param EventObject $event The event to process
     */
    public function getClientData($event)
    {
        Loader::loadModels($this, ['SupportManager.SupportManagerTickets']);
        $params = $event->getParams();
        $return = $event->getReturnVal();

        if (isset($params['client_id'])) {
            // Get all tickets for this client
            $i = 1;
            $data = ['tickets' => []];
            while (($tickets = $this->SupportManagerTickets->getList(
                null,
                null,
                $params['client_id'],
                $i++,
                ['last_reply_date' => 'desc'],
                true,
                ['reply']
            ))) {
                $data['tickets'] = array_merge($data['tickets'], $tickets);
            }

            // Filter out sensitive or internal data from the ticket
            foreach ($data['tickets'] as $index => $ticket) {
                $data['tickets'][$index] = $this->filterTicketExportData($ticket);
            }

            if (!isset($return['extra'])) {
                $return['extra'] = [];
            }
            $return['extra'][] = $data;
        }

        $event->setReturnVal($return);
    }

    /**
     * Strips out any sensitive or internal data from the given ticket
     *
     * @param stdClass $ticket The ticket to remove data from
     */
    private function filterTicketExportData(stdClass $ticket)
    {
        $ticket_whitelist = ['code', 'email', 'summary', 'priority', 'status', 'date_added', 'date_closed', 'replies'];
        $reply_whitelist = ['type', 'details', 'date_added', 'attachments'];
        $attachment_whitelist = ['name'];

        // Filter out unnecessary ticket data
        foreach ($ticket as $property => $value) {
            if (!in_array($property, $ticket_whitelist)) {
                unset($ticket->{$property});
            }
        }

        // Filter out unnecessary reply data
        foreach ($ticket->replies as $reply) {
            foreach ($reply as $property => $value) {
                if (!in_array($property, $reply_whitelist)) {
                    unset($reply->{$property});
                }
            }

            // Filter out unnecessary attachment data
            foreach ($reply->attachments as $attachment) {
                foreach ($attachment as $property => $value) {
                    if (!in_array($property, $attachment_whitelist)) {
                        unset($attachment->{$property});
                    }
                }
            }
        }

        return $ticket;
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
                $params['base_uri'] . 'plugin/support_manager/admin_tickets/search/'
                => Language::_('SupportManagerPlugin.event_getsearchoptions.tickets', true)
            ];
        }

        $event->setParams($params);
    }

    /**
     * Deletes all tickets, attachments, and replies associated with this client
     *
     * @param EventObject $event The event to process
     */
    public function deleteClientTickets($event)
    {
        Loader::loadModels($this, ['SupportManager.SupportManagerTickets']);
        $params = $event->getParams();

        if (isset($params['client_id'])) {
            while (($tickets = $this->SupportManagerTickets->getList(null, null, $params['client_id']))) {
                $ticket_ids = [];
                foreach ($tickets as $ticket) {
                    // Keep track of the tickets to delete
                    $ticket_ids[] = $ticket->id;

                    // Look at each reply for this ticket
                    foreach ($ticket->replies as $reply) {
                        // Unlink attachment files
                        foreach ($reply->attachments as $attachment) {
                            unlink($attachment->file_name);
                        }
                    }
                }

                // Delete tickets, replies, and attachment records
                $this->SupportManagerTickets->delete($ticket_ids);
            }
        }
    }

    /**
     * Execute the cron task
     *
     * @param string $key The cron task to execute
     */
    public function cron($key)
    {
        switch ($key) {
            case 'poll_tickets':
                Loader::loadModels($this, ['SupportManager.SupportManagerDepartments']);

                // Set options when processing emails
                $webdir = $this->SupportManagerDepartments->getWebDirectory();
                $is_cli = (empty($_SERVER['REQUEST_URI']));

                // Set the URIs to the admin/client portals
                $options = [
                    'is_cli' => $is_cli,
                    'client_uri' => $webdir . Configure::get('Route.client') . '/',
                    'admin_uri' => $webdir . Configure::get('Route.admin') . '/'
                ];

                Loader::loadComponents($this, ['SupportManager.TicketManager']);
                $this->TicketManager->setOptions($options);
                $this->TicketManager->processDepartmentEmails();
                break;
            case 'close_tickets':
                $this->cronCloseTickets();
                break;
            case 'delete_tickets':
                $this->cronDeleteTickets();
                break;
            default:
                break;
        }
    }

    /**
     * Performs the close tickets action
     */
    private function cronCloseTickets()
    {
        // Fetch all departments
        Loader::loadModels($this, ['SupportManager.SupportManagerDepartments', 'SupportManager.SupportManagerTickets']);
        $departments = $this->SupportManagerDepartments->getAll(Configure::get('Blesta.company_id'));

        foreach ($departments as $department) {
            $this->SupportManagerTickets->closeAllByDepartment($department->id);
        }
    }

    /**
     * Performs the delete tickets action
     */
    private function cronDeleteTickets()
    {
        // Fetch all departments
        Loader::loadModels($this, ['SupportManager.SupportManagerDepartments', 'SupportManager.SupportManagerTickets']);
        $departments = $this->SupportManagerDepartments->getAll(Configure::get('Blesta.company_id'));

        foreach ($departments as $department) {
            $this->SupportManagerTickets->deleteAllByDepartment($department->id);
        }
    }

    /**
     * Retrieves cron tasks available to this plugin along with their default values
     *
     * @return array A list of cron tasks
     */
    private function getCronTasks()
    {
        return [
            // Cron task to check for incoming email tickets
            [
                'key' => 'poll_tickets',
                'task_type' => 'plugin',
                'dir' => 'support_manager',
                'name' => Language::_('SupportManagerPlugin.cron.poll_tickets_name', true),
                'description' => Language::_('SupportManagerPlugin.cron.poll_tickets_desc', true),
                'type' => 'interval',
                'type_value' => 5,
                'enabled' => 1
            ],
            // Cron task to auto-close tickets
            [
                'key' => 'close_tickets',
                'task_type' => 'plugin',
                'dir' => 'support_manager',
                'name' => Language::_('SupportManagerPlugin.cron.close_tickets_name', true),
                'description' => Language::_('SupportManagerPlugin.cron.close_tickets_desc', true),
                'type' => 'interval',
                'type_value' => 360, // 6 hours
                'enabled' => 1
            ],
            // Cron task to auto-deletes tickets
            [
                'key' => 'delete_tickets',
                'task_type' => 'plugin',
                'dir' => 'support_manager',
                'name' => Language::_('SupportManagerPlugin.cron.delete_tickets_name', true),
                'description' => Language::_('SupportManagerPlugin.cron.delete_tickets_desc', true),
                'type' => 'interval',
                'type_value' => 360, // 6 hours
                'enabled' => 1
            ]
        ];
    }

    /**
     * Attempts to add new cron tasks for this plugin
     *
     * @param array $tasks A list of cron tasks to add
     * @see SupportManagerPlugin::install(), SupportManagerPlugin::upgrade(), SupportManagerPlugin::getCronTasks()
     */
    private function addCronTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            $task_id = $this->CronTasks->add($task);

            if (!$task_id) {
                $cron_task = $this->CronTasks->getByKey($task['key'], $task['dir'], $task['task_type']);
                if ($cron_task) {
                    $task_id = $cron_task->id;
                }
            }

            if ($task_id) {
                $task_vars = ['enabled' => $task['enabled']];
                if ($task['type'] == 'interval') {
                    $task_vars['interval'] = $task['type_value'];
                } else {
                    $task_vars['time'] = $task['type_value'];
                }

                $this->CronTasks->addTaskRun($task_id, $task_vars);
            }
        }
    }
}
