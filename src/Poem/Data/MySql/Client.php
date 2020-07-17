<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\Collection;
use Poem\Data\Connection;

class Client implements Connection {
    
    /**
     * MySql connection
     * 
     * @var PDO
     */
    protected $connection;


    /**
     * Defaults
     * 
     * @var array
     */
    protected $defaultSettings = [
        'host' => 'localhost',
        'username' => 'root',
    ];

    /**
     * Stored collection instances
     * 
     * @var array
     */
    private $collections = [];

    function connect(array $config) 
    {
        extract(array_merge($this->defaultSettings, $config));
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $this->connection->setAttribute( 
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION 
        );
    }

    function createCollection($name, array $schema = null) 
    {
        $sql = "CREATE TABLE `" . $name . "`(%s)";
        $fields = [];

        if($schema) {
            foreach($schema as $field => $type) {
                switch($type) {
                    case 'pk':
                        $fields[] = $field . " INT(11) AUTO_INCREMENT PRIMARY KEY";
                    break;
                    case 'string':
                        $fields[] = $field . " VARCHAR(180) NOT NULL";
                    break;
                    case 'date':
                        $fields[] = $field . " DATETIME()";
                    break;
                    default:
                        $fields[] = $field . " " . $type;
                }
            }
        }

        $this->connection->exec(sprintf($sql, implode(',', $fields)));
    }

    function accessCollection($name): Collection 
    {
        if(isset($this->collections[$name])) {
            return $this->collections[$name];
        }

        return $this->collections[$name] = new Table($name, $this->connection);
    }
}