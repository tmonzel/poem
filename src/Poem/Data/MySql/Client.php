<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\Client as ClientInterface;
use Poem\Data\Collection;

class Client implements ClientInterface {
    private $connection;

    function establishConnection(array $config) {
        extract($config);
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    }

    function getCollection($name): Collection {
        return new Table($name, $this->connection);
    }
}