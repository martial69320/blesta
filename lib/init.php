<?php
error_reporting(-1);

// include autoloader
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR
    . 'vendors' . DIRECTORY_SEPARATOR . 'autoload.php';

// Fetch available services
$services = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config'
    . DIRECTORY_SEPARATOR . 'services.php';

// Initialize
$container = new Minphp\Container\Container();

// Set services
foreach ($services as $service) {
    $container->register(new $service());
}

// Run bridge
$bridge = Minphp\Bridge\Initializer::get();
$bridge->setContainer($container);
$bridge->run();

// Set the container
Configure::set('container', $container);

return $container;
