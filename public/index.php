<?php

use Composer\Autoload\ClassLoader;
use Poem\Story;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Poem\\', __DIR__ . '/../src/Poem');
$loader->addPsr4(null, __DIR__ . '/../app');

const ENV_FILE = __DIR__ . "/../env.php";

if(file_exists(ENV_FILE)) {
    $config = require_once ENV_FILE;

    foreach($config as $k => $v) {
        putenv("$k=$v");
    }
}

// Prepare data clients
$clients = Data::clients();
$clients->addClient(new MySqlClient([
    "host" => getenv('DB_HOST'),
    "database" => getenv('DB_NAME'),
    "username" => getenv('DB_USER'),
    "password" => getenv('DB_PASSWORD') 
]));

// Tell a new story
$story = Story::new();
$story->about(Product\Actor::class);
$story->about(User\Actor::class);
$story->about(Retailer\Actor::class);
$story->about(Market\Actor::class);
$story->tell();
