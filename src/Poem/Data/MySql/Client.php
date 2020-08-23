<?php

namespace Poem\Data\MySql;

use PDO;
use PDOStatement;
use Poem\Data\CollectionAdapter;
use Poem\Data\Connection;
use Poem\Data\Field;

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
     * Stored table instances
     * 
     * @var array
     */
    private $tables = [];

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

    function truncate($name) 
    {
        return $this->query("TRUNCATE $name");
    }

    function createTable($name, array $schema = null) 
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

    function accessAdapter(string $type): CollectionAdapter
    {
        if(isset($this->tables[$type])) {
            return $this->tables[$type];
        }

        return $this->tables[$type] = new Table($type, $this);
    }

    function translateFieldType(string $type) {
        switch($type) {
            case Field::PRIMARY_KEY:
                return "INT(11) AUTO_INCREMENT PRIMARY KEY";
            break;
            case Field::FOREIGN_KEY:
                return "INT(11)";
            break;
            case Field::STR:
                return "VARCHAR(180) NOT NULL";
            break;
            case Field::DATE:
                return "DATETIME NOT NULL";
        }

        return $type;
    }
}