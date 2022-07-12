<?php
namespace Blesta\Core\ServiceProviders;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Configure;

/**
 * Application service provider
 *
 * @package blesta
 * @subpackage blesta.core.ServiceProviders
 * @copyright Copyright (c) 2019, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class App implements ServiceProviderInterface
{
    /**
     * @var Pimple\Container An instance of the container
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        $this->container = $container;
        $this->extendMinphp();
    }

    /**
     * Extends the minphp bridge services
     */
    private function extendMinphp()
    {
        // Enable 404 forwarding
        $this->container->extend(
            'minphp.mvc',
            function ($instance, $c) {
                $instance['404_forwarding'] = true;

                return $instance;
            }
        );

        // Set additional constants
        $this->container->extend(
            'minphp.constants',
            function ($instance, $c) {
                // Determine the document root directory by striping the web directory off of the root web directory
                // This may be inaccurate when run in CLI-mode since the web directory is the current working directory
                $doc = str_replace('/', DIRECTORY_SEPARATOR, str_replace('index.php/', '', $instance['WEBDIR']));

                $docRootDir = rtrim(
                    str_replace($doc == DIRECTORY_SEPARATOR ? '' : $doc, '', $instance['ROOTWEBDIR']),
                    DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;

                // Set additional constants
                $constants = [
                    'COREDIR' => $instance['ROOTWEBDIR'] . 'core' . DIRECTORY_SEPARATOR,
                    'DOCROOTDIR' => $docRootDir
                ];

                return array_merge($instance, $constants);
            }
        );

        // Set session TTL overrides
        $this->container->extend(
            'minphp.session',
            function ($instance, $c) {
                Configure::load('blesta');

                // Determine the TTLs and which to set for the database session
                // Default to minphp's values otherwise
                $sessionTtl = Configure::get('Blesta.session_ttl');
                $cookieTtl = Configure::get('Blesta.cookie_ttl');
                $ttls = [
                    'ttl' => ($sessionTtl && is_numeric($sessionTtl) ? (int)$sessionTtl : $instance['ttl']),
                    'cookie_ttl' => ($cookieTtl && is_numeric($cookieTtl) ? (int)$cookieTtl : $instance['cookie_ttl'])
                ];

                $dbTtl = (isset($_COOKIE[$instance['cookie_name']]) ? $ttls['cookie_ttl'] : $ttls['ttl']);
                $ttls['db'] = array_merge($instance['db'], ['ttl' => $dbTtl]);

                return array_merge($instance, $ttls);
            }
        );
    }
}
