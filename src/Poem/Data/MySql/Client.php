<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\Collection;
use Poem\Data\Connection;

class Client implements Connection {
    private $connection;

    protected $collections = [];

    function connect(array $config) {
        extract($config);
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    }

    function accessCollection($name): Collection {
        if(isset($this->collections[$name])) {
            return $this->collections[$name];
        }

        return $this->collections[$name] = new Table($name, $this->connection);
    }
}