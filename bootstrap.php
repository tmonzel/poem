<?php

use Composer\Autoload\ClassLoader;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;

const PROJECT_DIR = __DIR__;
const APP_DIR = __DIR__ . '/app';
const ENV_FILE = __DIR__ . "/env.php";

/** @var ClassLoader $loader */
$loader = require PROJECT_DIR . '/vendor/autoload.php';
$loader->addPsr4('Poem\\', PROJECT_DIR . '/src/Poem');
$loader->addPsr4(null, APP_DIR);

if(file_exists(ENV_FILE)) {
    foreach(require(ENV_FILE) as $k => $v) {
        putenv("$k=$v");
    }
}

// Register data connection
Data::registerConnection(MySqlClient::class, [
    "host" => getenv('DB_HOST'),
    "database" => getenv('DB_NAME'),
    "username" => getenv('DB_USER'),
    "password" => getenv('DB_PASSWORD') 
]);