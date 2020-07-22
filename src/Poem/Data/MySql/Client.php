<?php

namespace Poem\Data\MySql;

use PDO;
use PDOStatement;
use Poem\Data\CollectionAdapter;
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

    protected $settings = [];

    /**
     * Stored collection instances
     * 
     * @var array
     */
    private $collections = [];

    function connect(array $config) 
    {
        $this->settings = array_merge($this->defaultSettings, $config);
        extract($this->settings);
        
        $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $this->connection->setAttribute( 
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION 
        );
    }

    /**
     * Executes an SQL statement
     * 
     * @param string $sql,
     * @param array $params
     * @return PDOStatement|bool
     */
    function query(string $sql, array $params = null) 
    {
        if(isset($params)) {
            $statement = $this->connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        }

        return $this->connection->query($sql);
    }

    /**
     * 
     * @return string
     */
    function lastInsertId() 
    {
        return $this->connection->lastInsertId();
    }

    function syncSchema($name, array $schema) 
    {
        $collection = $this->accessCollection($name);
        
        if($collection->exists()) {
            // sync
            $collection->sync($schema);
        } else {
            $this->createCollection($name, $schema);
        }
    }

    function createCollection($name, array $schema = null) 
    {
        $sql = "CREATE TABLE `" . $name . "`(%s)";
        $fields = [];

        if($schema) {
            foreach($schema as $field => $type) {
                $fields[] = $field . " " . $this->translateFieldType($type);
            }
        }

        $this->query(sprintf($sql, implode(',', $fields)));
    }

    function accessCollection($name): CollectionAdapter 
    {
        if(isset($this->collections[$name])) {
            return $this->collections[$name];
        }

        return $this->collections[$name] = new Table($name, $this);
    }

    function translateFieldType(string $type) {
        switch($type) {
            case 'pk':
                return "INT(11) AUTO_INCREMENT PRIMARY KEY";
            break;
            case 'fk':
                return "INT(11)";
            break;
            case 'string':
                return "VARCHAR(180) NOT NULL";
            break;
            case 'date':
                return "DATETIME()";
        }

        return $type;
    }
}