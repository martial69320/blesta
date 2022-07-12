<?php
/**
 * SupportManagerDepartments model
 *
 * @package blesta
 * @subpackage blesta.plugins.support_manager
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class SupportManagerDepartments extends SupportManagerModel
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        Language::loadLang('support_manager_departments', null, PLUGINDIR . 'support_manager' . DS . 'language' . DS);
    }

    /**
     * Adds a department
     *
     * @param array $vars A list of input vars including:
     *  - company_id The company ID
     *  - name The department's name
     *  - description The department's description
     *  - email The department email address
     *  - method The method for sending email ('pipe' 'pop3', 'imap', 'none')
     *  - default_priority The default department ticket priority ('emergency', 'critical', 'high', 'medium', 'low')
     *  - host The email hostname (optional, required if method is not 'pipe')
     *  - user The email user (optional, required if method is not 'pipe')
     *  - pass The email pass (optional, required if method is not 'pipe')
     *  - port The email port (optional, required if method is not 'pipe')
     *  - security The security type (optional, required if method is not 'pipe')
     *  - box_name The box name type (optional)
     *  - mark_messages The message type (optional, required if method is not 'pipe')
     *  - clients_only (optional, defaults to '1')
     *  - override_from_email Whether or not to use the department's email address as the
     *      from address in email templates (optional, defaults to '1')
     *  - close_ticket_interval The number of minutes since the last reply to a ticket at
     *      which point the ticket can be closed automatically (optional, default null for never)
     *  - automatic_transition Whether or not to automatically change the status of tickets in this department
     *      to awaiting_reply after an admin replies to it
     *  - response_id The ID of the predefined response to use when a ticket is auto-closed
     *  - status The department status ('hidden' or 'visible')
     * @return stdClass The stdClass object representing the newly-created department, or void on error
     */
    public function add(array $vars)
    {
        $this->Input->setRules($this->getRules($vars));

        if ($this->Input->validates($vars)) {
            $fields = [
                'company_id', 'name', 'description', 'email', 'method',
                'default_priority', 'host', 'user', 'pass', 'port', 'security',
                'box_name', 'mark_messages', 'clients_only', 'override_from_email',
                'send_ticket_received', 'close_ticket_interval', 'delete_ticket_interval',
                'automatic_transition', 'response_id', 'status'
            ];

            $this->Record->insert('support_departments', $vars, $fields);

            return $this->get($this->Record->lastInsertId());
        }
    }

    /**
     * Edits a department
     *
     * @param int $department_id The ID of the department to update
     * @param array $vars A list of input vars including:
     *  - name The department's name
     *  - description The department's description
     *  - email The department email address
     *  - method The method for sending email ('pipe' 'pop3', 'imap', 'none')
     *  - default_priority The default department ticket priority ('emergency', 'critical', 'high', 'medium', 'low')
     *  - host The email hostname (optional, required if method is not 'pipe')
     *  - user The email user (optional, required if method is not 'pipe')
     *  - pass The email pass (optional, required if method is not 'pipe')
     *  - port The email port (optional, required if method is not 'pipe')
     *  - security The security type (optional, required if method is not 'pipe')
     *  - box_name The box name type (optional)
     *  - mark_messages The message type (optional, required if method is not 'pipe')
     *  - clients_only (optional, defaults to '1')
     *  - override_from_email Whether or not to use the department's email address as the
     *      from address in email templates (optional, defaults to '1')
     *  - close_ticket_interval The number of minutes since the last reply to a ticket at
     *      which point the ticket can be closed automatically (optional, default null for never)
     *  - automatic_transition Whether or not to automatically change the status of tickets in this department
     *      to awaiting_reply after an admin replies to it
     *  - response_id The ID of the predefined response to use when a ticket is auto-closed
     *  - status The department status ('hidden' or 'visible')
     * @return stdClass The stdClass object representing the newly-created department, or void on error
     */
    public function edit($department_id, array $vars)
    {
        $vars['department_id'] = $department_id;
        $this->Input->setRules($this->getRules($vars, true));

        if ($this->Input->validates($vars)) {
            $fields = [
                'name', 'description', 'email', 'method',
                'default_priority', 'host', 'user', 'pass', 'port',  'security',
                'box_name', 'mark_messages', 'clients_only', 'override_from_email',
                'send_ticket_received', 'close_ticket_interval', 'delete_ticket_interval',
                'automatic_transition', 'response_id', 'status'
            ];

            $this->Record->where('id', '=', $department_id)->
                update('support_departments', $vars, $fields);

            return $this->get($department_id);
        }
    }

    /**
     * Attempts to delete a support department
     *
     * @param int $department_id The ID of the department to delete
     */
    public function delete($department_id)
    {
        $rules = [
            'department_id' => [
                'has_tickets' => [
                    'rule' => [[$this, 'validateHasTickets']],
                    'negate' => true,
                    'message' => $this->_('SupportManagerDepartments.!error.department_id.has_tickets')
                ]
            ]
        ];

        $this->Input->setRules($rules);

        if ($this->Input->validates($vars)) {
            // Delete the department
            $this->Record->from('support_departments')->
                where('id', '=', $department_id)->delete();
        }
    }

    /**
     * Fetches a support department
     *
     * @param int $department_id The ID of the department to fetch
     * @return mixed An stdClass object representing the department, or false if none exist
     */
    public function get($department_id)
    {
        $fields = ['support_departments.*', 'COUNT(support_staff_departments.staff_id)' => 'assigned_staff'];
        $department = $this->Record->select($fields)
            ->from('support_departments')
            ->leftJoin(
                'support_staff_departments',
                'support_staff_departments.department_id',
                '=',
                'support_departments.id',
                false
            )
            ->where('id', '=', $department_id)
            ->group('support_departments.id')
            ->fetch();

        if ($department) {
            $department->pass = $this->systemDecrypt($department->pass);
        }

        return $department;
    }

    /**
     * Retrieves a list of departments
     *
     * @param int $company_id The ID of the company whose department list to fetch
     * @param int $page The page number of results to fetch (optional, default 1)
     * @param array $order A key/value pair array of fields to order the results by
     * @return array A list of stdClass objects, each representing a department
     */
    public function getList($company_id, $page = 1, array $order_by = ['name' => 'ASC'])
    {
        $this->Record = $this->getDepartments($company_id)->group('support_departments.id');

        if ($order_by) {
            $this->Record->order($order_by);
        }

        $departments = $this->Record->limit($this->getPerPage(), (max(1, $page) - 1)*$this->getPerPage())->fetchAll();
        foreach ($departments as &$department) {
            $department->pass = $this->systemDecrypt($department->pass);
        }

        return $departments;
    }

    /**
     * Retrieves the total number of departments
     *
     * @param int $company_id The ID of the company
     * @return int The total number of departments
     */
    public function getListCount($company_id)
    {
        return $this->getDepartments($company_id)->group('support_departments.id')->numResults();
    }

    /**
     * Fetches a list of all departments
     *
     * @param int $company_id The ID of the company whose departments to fetch
     * @param string $status The status of the support department
     *  (i.e. "visible", "hidden") (optional, default null for all)
     * @param bool $clients_only True to fetch only those departments for logged-in clients,
     *  false to fetch departments not for logged-in clients, or null for all (optional, default null)
     * @param array $criteria A list of filtering criteria including:
     *      - staff_id
     * @return array A list of stdClass objects, each representing a department
     */
    public function getAll($company_id, $status = null, $clients_only = null, array $criteria = [])
    {
        $this->Record = $this->getDepartments(
            $company_id,
            array_merge($criteria, ['status' => $status, 'clients_only' => $clients_only])
        );

        $departments = $this->Record->group('support_departments.id')->fetchAll();
        foreach ($departments as &$department) {
            $department->pass = $this->systemDecrypt($department->pass);
        }

        return $departments;
    }

    /**
     * Fetches the support department with the given email address and (optionally) method
     *
     * @param int $company_id The ID of the company whose departments to fetch
     * @param string $email The email address of the department to fetch
     * @param string $method The method of the support department, null for any:
     *  - pipe
     *  - pop3
     *  - imap
     *  - none
     * @return mixed A stdClass object representing the support department, false if no such department found
     */
    public function getByEmail($company_id, $email, $method = null)
    {
        $this->Record = $this->getDepartments($company_id);

        $this->Record->where('support_departments.email', '=', $email);

        if ($method) {
            $this->Record->where('support_departments.method', '=', $method);
        }

        if (($department = $this->Record->fetch())) {
            $department->pass = $this->systemDecrypt($department->pass);
        }

        return $department;
    }

    /**
     * Fetches a list of all departments by given methods
     *
     * @param int $company_id The ID of the compane whose departments to fetch
     * @param array $methods A list of method types to filter on (optional, null for all)
     * @return array A list of stdClass objects, each representing a department
     */
    public function getByMethod($company_id, array $methods = null)
    {
        $this->Record = $this->getDepartments($company_id);

        // Fetch by specific method types
        if (!empty($methods)) {
            $this->Record->open();

            $i = 0;
            foreach ($methods as $type) {
                if ($i++ == 0) {
                    $this->Record->where('method', '=', $type);
                } else {
                    $this->Record->orWhere('method', '=', $type);
                }
            }
            unset($i);

            $this->Record->close();
        }

        $departments = $this->Record->fetchAll();
        foreach ($departments as $department) {
            $department->pass = $this->systemDecrypt($department->pass);
        }

        return $departments;
    }

    /**
     * Fetches staff info for the staff member assigned to the given support department
     * and who has the given email address set as their email or mobile email address
     *
     * @param int $department_id The support department ID the staff member must belong to
     * @param string $email The email address the staff member must be assigned
     * @return mixed A stdClass object representing the staff member,
     *  false if the staff member does not exist, is not active, or does not belong to the department
     */
    public function getStaffByEmail($department_id, $email)
    {
        return $this->Record->select(['staff.*'])->
            from('support_staff_departments')->
            innerJoin('staff', 'staff.id', '=', 'support_staff_departments.staff_id', false)->
            where('staff.status', '=', 'active')->
            open()->
                where('staff.email', '=', $email)->
                orWhere('staff.email_mobile', '=', $email)->
            close()->
            where('support_staff_departments.department_id', '=', $department_id)->fetch();
    }

    /**
     * Retrieves a partially-constructed Record object for fetching departments
     *
     * @param int $company_id The ID of the company whose departments to fetch
     * @param array $criteria A list of filtering criteria including:
     *      - staff_id
     *      - status
     *      - clients_only
     * @return Record A partially-constructed Record object
     */
    private function getDepartments($company_id, array $criteria = [])
    {
        $fields = ['support_departments.*', 'COUNT(support_staff_departments.staff_id)' => 'assigned_staff'];
        $this->Record->select($fields)
            ->from('support_departments')
            ->leftJoin(
                'support_staff_departments',
                'support_staff_departments.department_id',
                '=',
                'support_departments.id',
                false
            )
            ->where('support_departments.company_id', '=', $company_id);

        if (isset($criteria['staff_id'])) {
            $this->Record->where('support_staff_departments.staff_id', '=', $criteria['staff_id']);
        }

        // Filter by status
        if (isset($criteria['status'])) {
            $this->Record->where('support_departments.status', '=', $criteria['status']);
        }

        // Filter by client access
        if (isset($criteria['clients_only'])) {
            $this->Record->where('support_departments.clients_only', '=', ($criteria['clients_only'] ? 1 : 0));
        }

        return $this->Record->group(['support_departments.id']);
    }

    /**
     * Retrieves a list of department intervals (in minutes) representing when a ticket can be closed automatically
     *
     * @return array A list of minutes and their language
     *
     * @deprecated since version 2.14.0
     */
    public function getCloseTicketIntervals()
    {
        return $this->getTicketIntervals(30);
    }

    /**
     * Retrieves a list of department intervals (in minutes)
     *
     * @param int $days Number of day intervals to fetch
     * @return array A list of minutes and their language
     */
    public function getTicketIntervals($days)
    {
        $options = [1 => $this->_('SupportManagerDepartments.ticket_intervals.day')];
        for ($i = 2; $i <= $days; $i++) {
            $options[($i * 60 * 24)] = $this->_('SupportManagerDepartments.ticket_intervals.days', $i);
        }

        return $options;
    }

    /**
     * Retrieves a list of department methods
     *
     * @return array A list of methods and their language
     */
    public function getMethods()
    {
        return [
            'none' => $this->_('SupportManagerDepartments.methods.none'),
            'pipe' => $this->_('SupportManagerDepartments.methods.pipe'),
            'pop3' => $this->_('SupportManagerDepartments.methods.pop3'),
            'imap' => $this->_('SupportManagerDepartments.methods.imap')
        ];
    }

    /**
     * Retrieves a list of department statuses
     *
     * @return array A list of statuses and their language
     */
    public function getStatuses()
    {
        return [
            'visible' => $this->_('SupportManagerDepartments.statuses.visible'),
            'hidden' => $this->_('SupportManagerDepartments.statuses.hidden')
        ];
    }

    /**
     * Retrieves a list of department priorities
     *
     * @return array A list of priorities and their language
     */
    public function getPriorities()
    {
        return [
            'emergency' => $this->_('SupportManagerDepartments.priorities.emergency'),
            'critical' => $this->_('SupportManagerDepartments.priorities.critical'),
            'high' => $this->_('SupportManagerDepartments.priorities.high'),
            'medium' => $this->_('SupportManagerDepartments.priorities.medium'),
            'low' => $this->_('SupportManagerDepartments.priorities.low')
        ];
    }

    /**
     * Retrieves a list of security types
     *
     * @return array A list of security types and their language
     */
    public function getSecurityTypes()
    {
        return [
            'none' => $this->_('SupportManagerDepartments.security_types.none'),
            'ssl' => $this->_('SupportManagerDepartments.security_types.ssl'),
            'tls' => $this->_('SupportManagerDepartments.security_types.tls')
        ];
    }

    /**
     * Retrieves a list of message types
     *
     * @return array A list of message types and their language
     */
    public function getMessageTypes()
    {
        return [
            'read' => $this->_('SupportManagerDepartments.message_types.read'),
            'deleted' => $this->_('SupportManagerDepartments.message_types.deleted')
        ];
    }

    /**
     * Fetches a list of rules for adding/editing a department
     *
     * @param array $vars A list of input vars
     * @param bool $edit True to get the edit rules, false for the add rules (optional, default false)
     */
    private function getRules(array $vars, $edit = false)
    {
        $rules = [
            'company_id' => [
                'exists' => [
                    'rule' => [[$this, 'validateExists'], 'id', 'companies'],
                    'message' => $this->_('SupportManagerDepartments.!error.company_id.exists')
                ]
            ],
            'name' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => $this->_('SupportManagerDepartments.!error.name.empty')
                ]
            ],
            'description' => [
                'empty' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => $this->_('SupportManagerDepartments.!error.description.empty')
                ]
            ],
            'email' => [
                'format' => [
                    'rule' => 'isEmail',
                    'message' => $this->_('SupportManagerDepartments.!error.email.format')
                ]
            ],
            'method' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['in_array', array_keys($this->getMethods())],
                    'message' => $this->_('SupportManagerDepartments.!error.method.format')
                ],
                'imap' => [
                    'if_set' => true,
                    'rule' => [[$this, 'validateImapRequired']],
                    'message' => $this->_('SupportManagerDepartments.!error.method.imap')
                ],
                'mailparse' => [
                    'if_set' => true,
                    'rule' => [[$this, 'validateMailparseRequired']],
                    'message' => $this->_('SupportManagerDepartments.!error.method.mailparse')
                ]
            ],
            'default_priority' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['in_array', array_keys($this->getPriorities())],
                    'message' => $this->_('SupportManagerDepartments.!error.default_priority.format')
                ]
            ],
            'host' => [
                'format' => [
                    'rule' => [[$this, 'validateHost'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.host.format')
                ],
                'length' => [
                    'rule' => ['maxLength', 128],
                    'message' => $this->_('SupportManagerDepartments.!error.host.length')
                ]
            ],
            'user' => [
                'format' => [
                    'rule' => [[$this, 'validateEmailCredential'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.user.format')
                ],
                'length' => [
                    'rule' => ['maxLength', 64],
                    'message' => $this->_('SupportManagerDepartments.!error.user.length')
                ]
            ],
            'pass' => [
                'format' => [
                    'rule' => [[$this, 'validateEmailCredential'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.password.format'),
                    'post_format' => [[$this, 'systemEncrypt']],
                    'last' => true
                ]
            ],
            'port' => [
                'format' => [
                    'rule' => [[$this, 'validatePort'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.port.format')
                ],
                'length' => [
                    'rule' => ['maxLength', 6],
                    'message' => $this->_('SupportManagerDepartments.!error.port.length')
                ]
            ],
            'security' => [
                'format' => [
                    'rule' => [[$this, 'validateSecurityType'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.security.format')
                ]
            ],
            'box_name' => [
                'format' => [
                    'if_set' => true,
                    'rule' => true,
                    'message' => '',
                    'post_format' => [[$this, 'getBoxName']]
                ]
            ],
            'mark_messages' => [
                'format' => [
                    'rule' => [[$this, 'validateMessageType'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.mark_messages.format')
                ],
                'valid' => [
                    'rule' => [[$this, 'validatePopMessageType'], $this->ifSet($vars['method'])],
                    'message' => $this->_('SupportManagerDepartments.!error.mark_messages.valid')
                ]
            ],
            'clients_only' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['in_array', [0,1]],
                    'message' => $this->_('SupportManagerDepartments.!error.clients_only.format')
                ]
            ],
            'override_from_email' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['in_array', [0,1]],
                    'message' => $this->_('SupportManagerDepartments.!error.override_from_email.format')
                ]
            ],
            'send_ticket_received' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['in_array', [0,1]],
                    'message' => $this->_('SupportManagerDepartments.!error.send_ticket_received.format')
                ]
            ],
            'close_ticket_interval' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['matches', '/^[1-9]+[0-9]*$/'],
                    'message' => $this->_('SupportManagerDepartments.!error.close_ticket_interval.format')
                ]
            ],
            'delete_ticket_interval' => [
                'format' => [
                    'if_set' => true,
                    'rule' => ['matches', '/^[1-9]+[0-9]*$/'],
                    'message' => $this->_('SupportManagerDepartments.!error.delete_ticket_interval.format')
                ]
            ],
            'response_id' => [
                'format' => [
                    'if_set' => true,
                    'rule' => [
                        [$this, 'validatePredefinedResponse'],
                        $this->ifSet($vars['company_id']),
                        $edit,
                        $this->ifSet($vars['department_id'])
                    ],
                    'message' => $this->_('SupportManagerDepartments.!error.response_id.format')
                ]
            ],
            'status' => [
                'format' => [
                    'rule' => ['in_array', array_keys($this->getStatuses())],
                    'message' => $this->_('SupportManagerDepartments.!error.status.format')
                ]
            ]
        ];

        if ($edit) {
            // Remove unnecessary rules
            unset($rules['company_id']);

            // Set all rules to optional
            $rules = $this->setRulesIfSet($rules);

            // Require a valid department ID
            $rules['department_id'] = [
                'exists' => [
                    'rule' => [[$this, 'validateExists'], 'id', 'support_departments'],
                    'message' => $this->_('SupportManagerDepartments.!error.department_id.exists')
                ]
            ];
        }

        return $rules;
    }

    /**
     * Gets the box name to use for the support department based on input
     *
     * @param mixed $box_name The box name to use for the department
     * @param string $default_box The default box name to use if $box_name is empty
     * @return string The box name to use for the department
     */
    public function getBoxName($box_name, $default_box = 'INBOX')
    {
        return (empty($box_name) ? $default_box : $box_name);
    }

    /**
     * Validates the host based on the method
     *
     * @param string $host The hostname
     * @param string $method The email method
     *
     * @return bool True if the host validates, false otherwise
     */
    public function validateHost($host, $method)
    {
        // Host must be set if pipe is not set
        if ($method != 'pipe' && $method != 'none' && empty($host)) {
            return false;
        }

        return (empty($host)
            || $this->Input->matches(
                $host,
                "/^([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9])(\.([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9]))+$/i"
            )
        );
    }

    /**
     * Validates the username or password based on the method
     *
     * @param string $field The field to validate
     * @param string $method The email method
     *
     * @return bool True if the field validates, false otherwise
     */
    public function validateEmailCredential($field, $method)
    {
        // Host must be set if pipe is not set
        if ($method != 'pipe' && $method != 'none' && empty($field)) {
            return false;
        }

        return true;
    }

    /**
     * Validates the port based on the method
     *
     * @param string $port The port number
     * @param string $method The email method
     *
     * @return bool True if the port validates, false otherwise
     */
    public function validatePort($port, $method)
    {
        // Host must be set if pipe is not set
        if ($method != 'pipe' && $method != 'none' && empty($port)) {
            return false;
        }

        return (empty($port) || $this->Input->matches($port, '/^[0-9]+$/'));
    }

    /**
     * Validates the security based on the method
     *
     * @param string $security The security type
     * @param string $method The email method
     * @return bool True if the security type validates, false otherwise
     */
    public function validateSecurityType($security, $method)
    {
        // Host must be set if pipe is not set
        if ($method != 'pipe' && $method != 'none' && empty($security)) {
            return false;
        }

        return in_array($security, array_keys($this->getSecurityTypes()));
    }

    /**
     * Validates the mark_messages type based on the method
     *
     * @param string $mark_messages The status of messages
     * @param string $method The email method
     * @return bool True if the message type validates, false otherwise
     */
    public function validateMessageType($mark_messages, $method)
    {
        // Host must be set if pipe is not set
        if ($method != 'pipe' && $method != 'none' && empty($mark_messages)) {
            return false;
        }

        return in_array($mark_messages, array_keys($this->getMessageTypes()));
    }

    /**
     * Validates the mark_messages type to ensure it is a valid type based on the method
     *
     * @param string $mark_messages The status of messages
     * @param string $method The method type
     * @return bool True if the message type validates, false otherwise
     */
    public function validatePopMessageType($mark_messages, $method)
    {
        // POP3 may only have messages set to 'deleted'
        if ($method == 'pop3' && $mark_messages != 'deleted') {
            return false;
        }
        return true;
    }

    /**
     * Validates whether the given department has tickets assigned to it
     *
     * @param int $department_id The ID of the department
     * @return bool True if the department has tickets assigned to it, false otherwise
     */
    public function validateHasTickets($department_id)
    {
        $num_tickets = $this->Record->select('support_tickets.*')->from('support_departments')->
            innerJoin('support_tickets', 'support_tickets.department_id', '=', 'support_departments.id', false)->
            where('support_departments.id', '=', $department_id)->
            numResults();

        if ($num_tickets > 0) {
            return true;
        }
        return false;
    }

    /**
     * Validates that the imap extension exists
     *
     * @param string $method The email handling method ('none', 'pipe', 'imap', 'pop3')
     * @return bool True if the imap extension exists or is not required for the given $method
     */
    public function validateImapRequired($method)
    {
        if ($method != 'none' && $method != 'pipe') {
            // imap extension required
            return extension_loaded('imap');
        }
        return true;
    }

    /**
     * Validates that the mailparse extension exists
     *
     * @param string $method The email handling method ('none', 'pipe', 'imap', 'pop3')
     * @return bool True if the mailparse extension exists or is not required for the given $method
     */
    public function validateMailparseRequired($method)
    {
        if ($method != 'none') {
            // mailparse extension required
            return extension_loaded('mailparse');
        }
        return true;
    }

    /**
     * Validates that the given predefined response can be used for this company
     *
     * @param int $response_id The ID of the predefined response to validate
     * @param int $company_id The ID of the company being assigned the response (optional, required if $edit is false)
     * @param bool $edit True if editing an existing department, false otherwise
     * @param int $department_id The ID of the department the response is being assigned to
     *  (optional, required if $edit is true)
     */
    public function validatePredefinedResponse($response_id, $company_id = null, $edit = false, $department_id = null)
    {
        if (!isset($this->SupportManagerResponses)) {
            Loader::loadModels($this, ['SupportManager.SupportManagerResponses']);
        }

        $response = $this->SupportManagerResponses->get($response_id);
        $valid = false;

        // Determine whether the response can be assigned to the department
        if ($response) {
            if ($edit && !empty($department_id)) {
                $department = $this->get($department_id);
                if ($department && $department->company_id == $response->company_id) {
                    $valid = true;
                }
            } elseif ($company_id == $response->company_id) {
                $valid = true;
            }
        }

        return $valid;
    }
}
