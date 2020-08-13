<?php

use Poem\Data;
use Poem\Model;
use Poem\Actor;
use Poem\Data\MySql\Client as MySqlClient;
use Poem\Director;
use Poem\Module;

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
        "host" => getenv('DB_HOST'),
        "database" => getenv('DB_NAME'),
        "username" => getenv('DB_USER'),
        "password" => getenv('DB_PASSWORD') 
    ]);

});

$director->add(Module\Worker::class, function(Module\Worker $worker) {
    
    // Register application actors
    $worker->register(Modules\User\Module::class);
    $worker->register(Modules\Retailer\Module::class);
    $worker->register(Modules\Product\Module::class);
    $worker->register(Modules\Market\Module::class);
    $worker->register(Modules\Order\Module::class);
    $worker->register(Modules\Info\Module::class);

});

$director->add(Model\Worker::class);

return $director;
