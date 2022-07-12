<?php

/**
 * Handles navigation.
 *
 * @package blesta
 * @subpackage blesta.app.models
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Navigation extends AppModel
{
    /**
     * @var An array of all preset base URIs
     */
    private $base_uris = [];

    /**
     * Initialize Navigation
     */
    public function __construct()
    {
        parent::__construct();
        Language::loadLang(['navigation']);
    }

    /**
     * Retrieves the primary navigation
     *
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array An array of main navigation elements in key/value pairs
     *  where each key is the URI and each value is an array representing that element including:
     *
     *  - name The name of the link
     *  - active True if the element is active
     *  - sub An array of subnav elements (optional) following the same indexes as above
     */
    public function getPrimary($base_uri)
    {
        $nav = [
            $base_uri => [
                'name' => $this->_('Navigation.getprimary.nav_home'),
                'active' => false,
                'sub' => [
                    $base_uri => [
                        'name' => $this->_('Navigation.getprimary.nav_home_dashboard'),
                        'active' => false
                    ]
                ]
            ],
            $base_uri . 'clients/' => [
                'name' => $this->_('Navigation.getprimary.nav_clients'),
                'active' => false,
                'sub' => [
                    $base_uri . 'clients/' => [
                        'name' => $this->_('Navigation.getprimary.nav_clients_browse'),
                        'active' => false
                    ]
                ]
            ],
            $base_uri . 'billing/' => [
                'name' => $this->_('Navigation.getprimary.nav_billing'),
                'active' => false,
                'sub' => [
                    $base_uri . 'billing/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_overview'),
                        'active' => false
                    ],
                    $base_uri . 'billing/invoices/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_invoices'),
                        'active' => false
                    ],
                    $base_uri . 'billing/transactions/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_transactions'),
                        'active' => false
                    ],
                    $base_uri . 'billing/services/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_services'),
                        'active' => false
                    ],
                    $base_uri . 'reports/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_reports'),
                        'active' => false
                    ],
                    $base_uri . 'billing/printqueue/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_printqueue'),
                        'active' => false
                    ],
                    $base_uri . 'billing/batch/' => [
                        'name' => $this->_('Navigation.getprimary.nav_billing_batch'),
                        'active' => false
                    ]
                ]
            ],
            $base_uri . 'packages/' => [
                'name' => $this->_('Navigation.getprimary.nav_packages'),
                'active' => false,
                'sub' => [
                    $base_uri . 'packages/' => [
                        'name' => $this->_('Navigation.getprimary.nav_packages_browse'),
                        'active' => false
                    ],
                    $base_uri . 'packages/groups/' => [
                        'name' => $this->_('Navigation.getprimary.nav_packages_groups'),
                        'active' => false
                    ],
                    $base_uri . 'package_options/' => [
                        'route' => [
                            'controller' => 'admin_package_options',
                            'action' => '*'
                        ],
                        'name' => $this->_('Navigation.getprimary.nav_package_options'),
                        'active' => false
                    ]
                ]
            ],
            $base_uri . 'tools/' => [
                'name' => $this->_('Navigation.getprimary.nav_tools'),
                'active' => false,
                'sub' => [
                    $base_uri . 'tools/logs/' => [
                        'name' => $this->_('Navigation.getprimary.nav_tools_logs'),
                        'active' => false
                    ],
                    $base_uri . 'tools/convertcurrency/' => [
                        'name' => $this->_('Navigation.getprimary.nav_tools_currency'),
                        'active' => false
                    ]
                ]
            ],
            // sets "settings" sub nav for admin_company_* controllers
            $base_uri . 'settings/company/' => [
                'route' => [
                    'controller' => 'admin_company_(.+)',
                    'action' => '*'
                ],
                'name' => '', // intentionally left blank so it won't render
                'active' => false,
                'sub' => [
                    $base_uri . 'settings/company/' => [
                        'route' => [
                            'controller' => 'admin_company_(.+)',
                            'action' => '*'
                        ],
                        'name' => $this->_('Navigation.getprimary.nav_settings_company'),
                        'active' => false
                    ],
                    $base_uri . 'settings/system/' => [
                        'route' => [
                            'controller' => 'admin_system_(.+)',
                            'action' => '*'
                        ],
                        'name' => $this->_('Navigation.getprimary.nav_settings_system'),
                        'active' => false
                    ]
                ]
            ],
            // sets "settings" sub nav for admin_system_* controllers
            $base_uri . 'settings/system/' => [
                'route' => [
                    'controller' => 'admin_system_(.+)',
                    'action' => '*'
                ],
                'name' => '', // intentionally left blank so it won't render
                'active' => false,
                'sub' => [
                    $base_uri . 'settings/company/' => [
                        'route' => [
                            'controller' => 'admin_company_(.+)',
                            'action' => '*'
                        ],
                        'name' => $this->_('Navigation.getprimary.nav_settings_company'),
                        'active' => false
                    ],
                    $base_uri . 'settings/system/' => [
                        'route' => [
                            'controller' => 'admin_system_(.+)',
                            'action' => '*'
                        ],
                        'name' => $this->_('Navigation.getprimary.nav_settings_system'),
                        'active' => false
                    ]
                ]
            ]
        ];

        // Set plugin primary nav elements
        $plugin_nav = $this->getPluginNav('nav_primary_staff');

        foreach ($plugin_nav as $element) {
            // Use the base URI configured for this element
            $parent_base_uri = $this->getElementBaseUri($base_uri, $this->ifSet($element->options['base_uri'], null));

            $nav[$parent_base_uri . $element->uri] = [
                'name' => $element->name,
                'active' => false
            ];

            // Set primary nav sub nav items if set
            if (isset($element->options['sub'])) {
                $nav[$parent_base_uri . $element->uri]['sub'] = [];
                foreach ($element->options['sub'] as $sub) {
                    // Use the base URI configured for this element
                    $sub_base_uri = $this->getElementBaseUri($parent_base_uri, $this->ifSet($sub['base_uri'], null));

                    $nav[$parent_base_uri . $element->uri]['sub'][$sub_base_uri . $sub['uri']] = [
                        'name' => $sub['name'],
                        'active' => false
                    ];
                }
            }
        }

        // Set plugin secondary nav elements
        $plugin_nav = $this->getPluginNav('nav_secondary_staff');

        foreach ($plugin_nav as $element) {
            if (!isset($element->options['parent'])) {
                continue;
            }

            // Use the base URI configured for this element
            $sub_base_uri = $this->getElementBaseUri($base_uri, $this->ifSet($element->options['base_uri'], null));

            if (isset($nav[$base_uri . $element->options['parent']])) {
                $nav[$base_uri . $element->options['parent']]['sub'][$sub_base_uri . $element->uri] = [
                    'name' => $element->name,
                    'active' => false
                ];
            }
        }
        return $nav;
    }

    /**
     * Retrieves the primary navigation for the client interface
     *
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array An array of main navigation elements in key/value pairs
     *  where each key is the URI and each value is an array representing that element including:
     *
     *  - name The name of the link
     *  - active True if the element is active
     *  - sub An array of subnav elements (optional) following the same indexes as above
     */
    public function getPrimaryClient($base_uri)
    {
        $nav = [
            $base_uri => [
                'name' => $this->_('Navigation.getprimaryclient.nav_dashboard'),
                'active' => false
            ],
            $base_uri . 'accounts/' => [
                'name' => $this->_('Navigation.getprimaryclient.nav_paymentaccounts'),
                'active' => false,
                'secondary' => [
                    $base_uri . 'accounts/' => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_paymentaccounts'),
                        'active' => false,
                        'icon' => 'fa fa-list'
                    ],
                    $base_uri . 'accounts/add/' => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_paymentaccounts_add'),
                        'active' => false,
                        'icon' => 'fa fa-plus-square'
                    ],
                    $base_uri => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_return'),
                        'active' => false,
                        'icon' => 'fa fa-arrow-left'
                    ]
                ]
            ],
            $base_uri . 'contacts/' => [
                'name' => $this->_('Navigation.getprimaryclient.nav_contacts'),
                'active' => false,
                'secondary' => [
                    $base_uri . 'contacts/' => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_contacts'),
                        'active' => false,
                        'icon' => 'fa fa-list'
                    ],
                    $base_uri . 'contacts/add/' => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_contacts_add'),
                        'active' => false,
                        'icon' => 'fa fa-plus-square'
                    ],
                    $base_uri => [
                        'name' => $this->_('Navigation.getprimaryclient.nav_return'),
                        'active' => false,
                        'icon' => 'fa fa-arrow-left'
                    ]
                ]
            ]
        ];

        // Include the primary client's plugin navigation
        return $this->getPluginNavPrimaryClient($nav, $base_uri);
    }

    /**
     * Updates the given $nav to append client primary navigation elements
     *
     * @param array $nav An array of main navigation elements in key/value pairs
     *  where each key is the URI and each value is an array representing that element including:
     *
     *  - name The name of the link
     *  - active True if the element is active
     *  - sub An array of subnav elements (optional) following the same indexes as above
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array An array of main navigation elements in key/value pairs
     *  where each key is the URI and each value is an array representing that element including:
     *
     *  - name The name of the link
     *  - active True if the element is active
     *  - sub An array of subnav elements (optional) following the same indexes as above
     */
    private function getPluginNavPrimaryClient(array $nav, $base_uri)
    {
        $plugin_nav = $this->getPluginNav('nav_primary_client');

        foreach ($plugin_nav as $element) {
            // Use the base URI configured for this element
            $parent_base_uri = $this->getElementBaseUri($base_uri, $this->ifSet($element->options['base_uri'], null));

            $nav[$parent_base_uri . $element->uri] = [
                'name' => $element->name,
                'active' => false,
                'icon' => isset($element->icon) ? $element->icon : null
            ];

            // Set secondary nav sub nav items if set
            if (isset($element->options['secondary'])) {
                $nav[$parent_base_uri . $element->uri]['secondary'] = [];
                foreach ($element->options['secondary'] as $sub) {
                    // Use the base URI configured for this element
                    $sub_base_uri = $this->getElementBaseUri($parent_base_uri, $this->ifSet($sub['base_uri'], null));

                    $nav[$parent_base_uri . $element->uri]['secondary'][$sub_base_uri . $sub['uri']] = [
                        'name' => $sub['name'],
                        'active' => false,
                        'icon' => isset($sub['icon']) ? $sub['icon'] : null
                    ];
                }
            }

            // Set primary nav sub nav items if set
            if (isset($element->options['sub'])) {
                $nav[$parent_base_uri . $element->uri]['sub'] = [];
                foreach ($element->options['sub'] as $sub) {
                    // Use the base URI configured for this element
                    $sub_base_uri = $this->getElementBaseUri($parent_base_uri, $this->ifSet($sub['base_uri'], null));

                    $nav[$parent_base_uri . $element->uri]['sub'][$sub_base_uri . $sub['uri']] = [
                        'name' => $sub['name'],
                        'active' => false,
                        'icon' => isset($sub['icon']) ? $sub['icon'] : null
                    ];
                }
            }
        }

        return $nav;
    }

    /**
     * Retrieves the navigation for unauthenticated clients
     *
     * @param string $base_uri The base_uri for the user not logged in
     * @param string $base_user_uri The base_uri for a logged in user
     * @return array An array of main navigation elements in key/value pairs
     *  where each key is the URI and each value is an array representing that element including:
     *
     *  - name The name of the link
     *  - active True if the element is active
     *  - sub An array of subnav elements (optional) following the same indexes as above
     */
    public function getPrimaryPublic($base_uri, $base_user_uri)
    {
        $nav = [
            $base_uri => [
                'name' => $this->_('Navigation.getprimarypublic.nav_dashboard'),
                'active' => false
            ]
        ];

        // Include the primary client's plugin navigation
        $nav = $this->getPluginNavPrimaryClient($nav, $base_user_uri);

        return $nav;
    }

    /**
     * Retrieves the navigation for company settings
     *
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array A numerically-indexed array of the company settings
     *  navigation where each element contains an array which includes:
     *
     *  - name The name of the element
     *  - class The CSS class name for the element
     *  - uri The URI for the element
     *  - children An array of child elements which follow the same indexes as above
     */
    public function getCompany($base_uri)
    {
        $nav = [
            [
                'name' => $this->_('Navigation.getcompany.nav_general'),
                'class' => '',
                'icon' => 'wrench',
                'uri' => $base_uri . 'settings/company/general/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_general_localization'),
                        'uri' => $base_uri . 'settings/company/general/localization/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_general_international'),
                        'uri' => $base_uri . 'settings/company/general/international/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_general_encryption'),
                        'uri' => $base_uri . 'settings/company/general/encryption/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_general_contacttypes'),
                        'uri' => $base_uri . 'settings/company/general/contacttypes/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_general_marketing'),
                        'uri' => $base_uri . 'settings/company/general/marketing/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_lookandfeel'),
                'class' => '',
                'icon' => 'desktop',
                'uri' => $base_uri . 'settings/company/themes/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_lookandfeel_themes'),
                        'uri' => $base_uri . 'settings/company/themes/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_lookandfeel_template'),
                        'uri' => $base_uri . 'settings/company/lookandfeel/template/'
                    ],
                /*
                  array(
                  'name' => $this->_("Navigation.getcompany.nav_lookandfeel_customize"),
                  'uri' => $base_uri . "settings/company/lookandfeel/customize/"
                  )
                 */
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_automation'),
                'class' => '',
                'icon' => 'clock-o',
                'uri' => $base_uri . 'settings/company/automation/'
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_billing'),
                'class' => '',
                'icon' => 'calculator',
                'uri' => $base_uri . 'settings/company/billing/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_invoices'),
                        'uri' => $base_uri . 'settings/company/billing/invoices/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_custominvoice'),
                        'uri' => $base_uri . 'settings/company/billing/customization/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_deliverymethods'),
                        'uri' => $base_uri . 'settings/company/billing/deliverymethods/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_acceptedtypes'),
                        'uri' => $base_uri . 'settings/company/billing/acceptedtypes/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_notices'),
                        'uri' => $base_uri . 'settings/company/billing/notices/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_billing_coupons'),
                        'uri' => $base_uri . 'settings/company/billing/coupons/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_modules'),
                'class' => '',
                'icon' => 'puzzle-piece',
                'uri' => $base_uri . 'settings/company/modules/'
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_gateways'),
                'class' => '',
                'icon' => 'university',
                'uri' => $base_uri . 'settings/company/gateways/'
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_taxes'),
                'class' => '',
                'icon' => 'money',
                'uri' => $base_uri . 'settings/company/taxes/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_taxes_basictax'),
                        'uri' => $base_uri . 'settings/company/taxes/basic/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_taxes_taxrules'),
                        'uri' => $base_uri . 'settings/company/taxes/rules/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_emails'),
                'class' => '',
                'icon' => 'envelope-o',
                'uri' => $base_uri . 'settings/company/emails/',
                'current' => false,
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_emails_templates'),
                        'uri' => $base_uri . 'settings/company/emails/templates/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_emails_mail'),
                        'uri' => $base_uri . 'settings/company/emails/mail/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_emails_signatures'),
                        'uri' => $base_uri . 'settings/company/emails/signatures/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_clientoptions'),
                'class' => '',
                'icon' => 'sliders',
                'uri' => $base_uri . 'settings/company/clientoptions/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_clientoptions_general'),
                        'uri' => $base_uri . 'settings/company/clientoptions/general/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_clientoptions_customfields'),
                        'uri' => $base_uri . 'settings/company/clientoptions/customfields/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_clientoptions_requiredfields'),
                        'uri' => $base_uri . 'settings/company/clientoptions/requiredfields/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_currencies'),
                'class' => '',
                'icon' => 'usd',
                'uri' => $base_uri . 'settings/company/currencies/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getcompany.nav_currency_currencysetup'),
                        'uri' => $base_uri . 'settings/company/currencies/setup/'
                    ],
                    [
                        'name' => $this->_('Navigation.getcompany.nav_currency_active'),
                        'uri' => $base_uri . 'settings/company/currencies/active/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_plugins'),
                'class' => '',
                'icon' => 'plug',
                'uri' => $base_uri . 'settings/company/plugins/'
            ],
            [
                'name' => $this->_('Navigation.getcompany.nav_groups'),
                'class' => '',
                'icon' => 'users',
                'uri' => $base_uri . 'settings/company/groups/'
            ]
        ];

        return $nav;
    }

    /**
     * Retrieves the navigation for system settings
     *
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array A numerically-indexed array of the system settings
     *  navigation where each element contains an array which includes:
     *
     *  - name The name of the element
     *  - class The CSS class name for the element
     *  - uri The URI for the element
     *  - children An array of child elements which follow the same indexes as above
     */
    public function getSystem($base_uri)
    {
        $nav = [
            [
                'name' => $this->_('Navigation.getsystem.nav_general'),
                'class' => '',
                'icon' => 'wrench',
                'uri' => $base_uri . 'settings/system/general/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getsystem.nav_general_basic'),
                        'uri' => $base_uri . 'settings/system/general/basic/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_general_geoip'),
                        'uri' => $base_uri . 'settings/system/general/geoip/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_general_maintenance'),
                        'uri' => $base_uri . 'settings/system/general/maintenance/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_general_license'),
                        'uri' => $base_uri . 'settings/system/general/license/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_general_paymenttypes'),
                        'uri' => $base_uri . 'settings/system/general/paymenttypes/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_automation'),
                'class' => '',
                'icon' => 'clock-o',
                'uri' => $base_uri . 'settings/system/automation/'
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_companies'),
                'class' => '',
                'icon' => 'building-o',
                'uri' => $base_uri . 'settings/system/companies'
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_backup'),
                'class' => '',
                'icon' => 'hdd-o',
                'uri' => $base_uri . 'settings/system/backup/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getsystem.nav_backup_index'),
                        'uri' => $base_uri . 'settings/system/backup/index/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_backup_ftp'),
                        'uri' => $base_uri . 'settings/system/backup/ftp/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_backup_amazon'),
                        'uri' => $base_uri . 'settings/system/backup/amazon/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_staff'),
                'class' => '',
                'icon' => 'user',
                'uri' => $base_uri . 'settings/system/staff/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getsystem.nav_staff_manage'),
                        'uri' => $base_uri . 'settings/system/staff/manage/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_staff_groups'),
                        'uri' => $base_uri . 'settings/system/staff/groups/'
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_api'),
                'class' => '',
                'icon' => 'code',
                'uri' => $base_uri . 'settings/system/api/'
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_upgrade'),
                'class' => '',
                'icon' => 'cloud-download',
                'uri' => $base_uri . 'settings/system/upgrade/'
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_help'),
                'class' => '',
                'icon' => 'medkit',
                'uri' => $base_uri . 'settings/system/help/',
                'children' => [
                    [
                        'name' => $this->_('Navigation.getsystem.nav_help_index'),
                        'uri' => $base_uri . 'settings/system/help/index/'
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_help_notes'),
                        'uri' => 'https://docs.blesta.com/display/support/Releases',
                        'attributes' => [
                            'target' => 'blank'
                        ]
                    ],
                    [
                        'name' => $this->_('Navigation.getsystem.nav_help_about'),
                        'uri' => $base_uri . 'settings/system/help/credits/',
                        'attributes' => ['rel' => 'modal']
                    ]
                ]
            ],
            [
                'name' => $this->_('Navigation.getsystem.nav_marketplace'),
                'class' => '',
                'icon' => 'shopping-cart',
                'uri' => Configure::get('Blesta.marketplace_url'),
                'attributes' => ['target' => '_blank']
            ]
        ];
        return $nav;
    }

    /**
     * Fetches all search options available to the current company
     *
     * @param string $base_uri The base_uri for the currently logged in user
     * @return array An array of search items in key/value pairs, where each
     *  key is the search type and each value is the language for the search type
     */
    public function getSearchOptions($base_uri = null)
    {
        $options = [
            'smart' => $this->_('Navigation.getsearchoptions.smart'),
            'clients' => $this->_('Navigation.getsearchoptions.clients'),
            'invoices' => $this->_('Navigation.getsearchoptions.invoices'),
            'transactions' => $this->_('Navigation.getsearchoptions.transactions'),
            'services' => $this->_('Navigation.getsearchoptions.services'),
            'packages' => $this->_('Navigation.getsearchoptions.packages')
        ];

        // Allow custom search options to be appended to the list of search options
        $eventFactory = $this->getFromContainer('util.events');
        $eventListener = $eventFactory->listener();
        $eventListener->register('Navigation.getSearchOptions');
        $event = $eventListener->trigger(
            $eventFactory->event('Navigation.getSearchOptions', compact('options', 'base_uri'))
        );

        $params = $event->getParams();

        if (isset($params['options'])) {
            $options = $params['options'];
        }

        return $options;
    }

    /**
     * Returns all plugin navigation for the requested location
     *
     * @param string $location The location to fetch plugin navigation for
     * @return array An array of plugin navigation
     */
    public function getPluginNav($location)
    {
        if (!isset($this->PluginManager)) {
            Loader::loadModels($this, ['PluginManager']);
        }

        return $this->PluginManager->getActions(Configure::get('Blesta.company_id'), $location, true);
    }

    /**
     * Adds a URI referenced by its label
     *
     * @param string $label The unique label to set for the URI
     * @param string $uri The URI
     * @return this
     */
    public function baseUri($label, $uri)
    {
        $this->base_uris[$label] = $uri;
        return $this;
    }

    /**
     * Retrieves the base URI to use for a navigation element.
     * Defaults to $base_uri if the element's base URI is unknown.
     *
     * @param string $base_uri The current base URI
     * @param string $element_base_uri The element's defined base URI
     * @return string The base URI for the current element
     */
    private function getElementBaseUri($base_uri, $element_base_uri = null)
    {
        return (isset($element_base_uri) ? $this->getBaseUri($element_base_uri) : $base_uri);
    }

    /**
     * Retrieves the base URI matching the given label.
     * If not found, returns back the given label.
     *
     * @param string $label The base URI label
     * @return string The base URI
     */
    private function getBaseUri($label)
    {
        return (array_key_exists($label, $this->base_uris) ? $this->base_uris[$label] : $label);
    }
}
