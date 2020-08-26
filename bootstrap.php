<?php

use Poem\Actor;
use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;
use Poem\Director;
use Poem\Model;

const PROJECT_DIR = __DIR__;
const APP_DIR = __DIR__ . '/app';
const ENV_FILE = __DIR__ . "/env.php";

if(file_exists(ENV_FILE)) {
    foreach(require(ENV_FILE) as $k => $v) {
        putenv("$k=$v");
    }
}

$director = Director::access();
$director->add(Data\Worker::class, function(Data\Worker $worker) {
    
    // Register mysql client to data worker
    $worker->registerConnection(MySqlClient::class, [
        'host' => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD') 
    ]);

});

$director->add(Model\Worker::class);
$director->add(Actor\Worker::class);
$director->add(Services\Mail\Service::class);

$loader = require(APP_DIR . '/Modules/loader.php');
$loader($director);

return $director;
