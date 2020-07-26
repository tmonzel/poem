<?php

use Poem\Data;
use Poem\Data\MySql\Client as MySqlClient;
use Poem\Director;

const PROJECT_DIR = __DIR__;
const APP_DIR = __DIR__ . '/app';
const ENV_FILE = __DIR__ . "/env.php";

if(file_exists(ENV_FILE)) {
    foreach(require(ENV_FILE) as $k => $v) {
        putenv("$k=$v");
    }
}

$director = new Director();
$director->hire(Data\Worker::class, function($worker) {
    
    // Register mysql client to data worker
    $worker->registerConnection(MySqlClient::class, [
        "host" => getenv('DB_HOST'),
        "database" => getenv('DB_NAME'),
        "username" => getenv('DB_USER'),
        "password" => getenv('DB_PASSWORD') 
    ]);

});

return $director;
