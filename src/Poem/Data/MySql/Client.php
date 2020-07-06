<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\Client as ClientInterface;
use Poem\Data\Collection;

class Client implements ClientInterface {
    private $config;
    private $connection;

    function __construct($config) {
        $this->config = $config;
        $this->establishConnection();
    }

    function establishConnection() {
        extract($this->config);
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    }

    function getCollection($name): Collection {
        return new Table($name, $this->connection);
    }
}