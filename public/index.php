<?php

use Composer\Autoload\ClassLoader;
use Poem\Story;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Poem\\', __DIR__ . '/../src/Poem');
$loader->addPsr4(null, __DIR__ . '/../app');


// A action based micro api framework
// rest, json, generators

$clients = Data::clients();
$clients->addClient(new MySqlClient([
    "host" => "localhost",
    "database" => "poem",
    "username" => "root",
    "password" => "" 
]));

$story = Story::create();
$story->about(Product\Actor::class);
$story->about(User\Actor::class);
$story->tell();
