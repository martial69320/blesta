<?php
Configure::set('interworx.map', [
    'module' => 'interworx',
    'module_row_key' => 'hostname',
    'module_row_meta' => [
        (object)['key' => 'server_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'host_name', 'value' => (object)['module' => 'hostname'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'key', 'value' => (object)['module' => 'password'], 'serialized' => 0, 'encrypted' => 1],
        (object)['key' => 'use_ssl', 'value' => (object)['module' => 'secure'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'port', 'value' => '2443', 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'account_limit', 'value' => (object)['module' => 'maxaccounts'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'debug', 'value' => 'none', 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'name_servers', 'value' => null, 'serialized' => 1, 'encrypted' => 0],
        (object)['key' => 'notes', 'value' => null, 'serialized' => 0, 'encrypted' => 0]
    ],
    'package_meta' => [
        (object)['key' => 'package', 'value' => (object)['package' => 'configoption1'], 'serialized' => 0, 'encrypted' => 0],
        (object)['key' => 'type', 'value' => 'standard', 'serialized' => 0, 'encrypted' => 0]
    ],
    'service_fields' => [
        'domain' => (object)['key' => 'interworx_domain', 'serialized' => 0, 'encrypted' => 0],
        'username' => (object)['key' => 'interworx_username', 'serialized' => 0, 'encrypted' => 0],
        'password' => (object)['key' => 'interworx_password', 'serialized' => 0, 'encrypted' => 1]
    ]
]);
