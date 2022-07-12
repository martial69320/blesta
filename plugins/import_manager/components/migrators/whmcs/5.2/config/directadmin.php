<?php
Configure::set('directadmin.map', [
    'module' => 'direct_admin',
    'module_row_key' => 'hostname',
    'module_row_meta' => [
        (object)['key' => 'server_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'host_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'user_name', 'value' => (object)['module' => 'username'], 'serialized' => 0, 'encrypted' => 1],
        (object)['key' => 'password', 'value' => (object)['module' => 'password'], 'serialized' => 0, 'encrypted' => 1],
        (object)['key' => 'use_ssl', 'value' => (object)['module' => 'secure'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'account_limit', 'value' => (object)['module' => 'maxaccounts'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'name_servers', 'value' =>null, 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'notes', 'value' => null, 'serialized' => 0, 'encrypted' => 0]
    ],
    'package_meta' => [
        (object)['key' => 'type', 'value' => 'user', 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'package', 'value' => (object)['package' => 'configoption1'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'ip', 'value' => (object)['package' => 'configoption2'], 'serialized' => 0, 'encrypted' => 0]
    ],
    'service_fields' => [
        'domain' => (object)['key' => 'direct_admin_domain', 'serialized' => 0, 'encrypted' => 0],
        'username' => (object)['key' => 'direct_admin_username', 'serialized' => 0, 'encrypted' => 0],
        'password' => (object)['key' => 'direct_admin_password', 'serialized' => 0, 'encrypted' => 1]
    ]
]);
