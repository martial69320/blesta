<?php
Configure::set('cpanel.map', [
    'module' => 'cpanel',
    'module_row_key' => 'hostname',
    'module_row_meta' => [
        (object)['key' => 'host_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'user_name', 'value' => (object)['module' => 'username'], 'serialized' => 0, 'encrypted' => 1],
        (object)['key' => 'key', 'value' => (object)['module' => 'accesshash'], 'serialized' => 0, 'encrypted' => 1],
        (object)['key' => 'use_ssl', 'value' => (object)['module' => 'secure'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'account_limit', 'value' => (object)['module' => 'maxaccounts'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'name_servers', 'value' => null, 'serialized' => 1, 'encrypted' => 0],
        (object)['key' => 'notes', 'value' => null, 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'server_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
    ],
    'package_meta' => [
        (object)['key' => 'package', 'value' => (object)['package' => 'configoption1'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'acl', 'value' => (object)['package' => 'configoption21'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'type', 'value' => 'standard', 'serialized' => 0, 'encrypted' => 0]
    ],
    'service_fields' => [
        'domain' => (object)['key' => 'cpanel_domain', 'serialized' => 0, 'encrypted' => 0],
        'username' => (object)['key' => 'cpanel_username', 'serialized' => 0, 'encrypted' => 0],
        'password' => (object)['key' => 'cpanel_password', 'serialized' => 0, 'encrypted' => 1]
    ]
]);
