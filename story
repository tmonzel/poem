#!/usr/bin/env php
<?php

const APP_ROOT = __DIR__;
const ENV_FILE = APP_ROOT . "/env.php";

if(file_exists(ENV_FILE)) {
    foreach(require(ENV_FILE) as $k => $v) {
        putenv("$k=$v");
    }
}

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/vendor/autoload.php';
$loader->addPsr4('Poem\\', __DIR__ . '/src/Poem');
$loader->addPsr4(null, __DIR__ . '/app');

use Poem\Console\MigrateCommand;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;
use Symfony\Component\Console\Application;

// Register data connection
Data::registerConnection(MySqlClient::class, [
    "host" => getenv('DB_HOST'),
    "database" => getenv('DB_NAME'),
    "username" => getenv('DB_USER'),
    "password" => getenv('DB_PASSWORD') 
]);

$application = new Application();
$application->add(new MigrateCommand());
$application->run();