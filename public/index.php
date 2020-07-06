<?php

use Composer\Autoload\ClassLoader;
use DI\Container;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;
use Slim\Factory\AppFactory;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Poem\\', __DIR__ . '/../src/Poem');
$loader->addPsr4(null, __DIR__ . '/../app');

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

$app = AppFactory::create();
//$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

function debug($value) {
    error_log( print_r($value, true) );
}

$clients = Data::clients();
$clients->addClient(new MySqlClient([
    "host" => "localhost",
    "database" => "poem",
    "username" => "root",
    "password" => "" 
]));

// A action based micro api framework
// rest, json, generators

// Introduce actors
Product\Actor::introduce($app);
User\Actor::introduce($app);

$app->run();
