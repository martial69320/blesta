<?php
/**
 * Order System Parent Controller
 *
 * @package blesta
 * @subpackage blesta.plugins.order
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class OrderController extends AppController
{
    public function preAction()
    {
        $this->structure->setDefaultView(APPDIR);
        parent::preAction();

        // Auto load language for the controller
        Language::loadLang(
            [Loader::fromCamelCase(get_class($this)), 'order_plugin'],
            null,
            dirname(__FILE__) . DS . 'language' . DS
        );

        // Override default view directory
        $this->view->view = 'default';
        $this->orig_structure_view = $this->structure->view;
        $this->structure->view = 'default';
    }

    /**
     * Retrieves the location of the given IP address
     *
     * @param string $ip_address The IP address
     * @param array $system_settings An array of the system settings (optional)
     * @return mixed False if geo IP is disabled or unavailable, otherwise an array containing:
     *  - location An array containing address information:
     *      - city
     *      - region
     *      - postal_code
     *      - country_name
     *      - latitude
     *      - longitude
     */
    protected function getGeoIp($ip_address, array $system_settings = null)
    {
        if (empty($ip_address)) {
            return false;
        }

        if (!isset($this->SettingsCollection)) {
            $this->components(['SettingsCollection']);
        }

        if (empty($system_settings)) {
            $system_settings = $this->SettingsCollection->fetchSystemSettings();
        }

        $geo_ip = [];
        if (isset($system_settings['geoip_enabled']) && $system_settings['geoip_enabled'] == 'true') {
            // Load GeoIP API
            $this->components(['Net']);
            if (!isset($this->NetGeoIp)) {
                $this->NetGeoIp = $this->Net->create('NetGeoIp');
            }

            try {
                $geo_ip = ['location' => $this->NetGeoIp->getLocation($ip_address)];
            } catch (Exception $e) {
                // IP address could not be determined
                return false;
            }

            return $geo_ip;
        }

        return false;
    }
}

require_once dirname(__FILE__) . DS . 'order_form_controller.php';
