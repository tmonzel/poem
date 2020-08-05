<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\CollectionAdapter;
use Poem\Data\Cursor;
use Poem\Set;

class Table implements CollectionAdapter 
{
    /**
     * Associated client
     * 
     * @var Client
     */
    protected $client;

    /**
     * Table name
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var array
     */
    protected $schema = [];

    /**
     * Create table instance.
     * 
     * @param string $name
     * @param Client $client
     */
    function __construct(string $name, Client $client) 
    {
        $this->name = $name;
        $this->client = $client;
    }


    function getType(): string
    {
        return $this->name;
    }

    /**
     * Ensure this table exists
     */
    function exists(): bool 
    {
        return $this->client->query(
            "SHOW TABLES LIKE '$this->name'"
        )->rowCount() > 0;
    }

    function dropField(string $name) 
    {
        return $this->client->query(
            "ALTER TABLE $this->name DROP COLUMN $name"
        );
    }

    function addField(string $name, string $type) 
    {
        return $this->client->query(
            "ALTER TABLE $this->name ADD COLUMN $name " . $this->client->translateFieldType($type)
        );
    }

    function fetchSchema(): array 
    {
        $columns = $this->client->query("SHOW COLUMNS FROM $this->name")->fetchAll(PDO::FETCH_ASSOC);
        $schema = [];

        foreach($columns as $column) {
            $schema[$column['Field']] = [
                'type' => $column['Type'],
                'default' => $column['Default'],
                'key' => $column['Key'],
                'extra' => $column['Extra']
            ];
        }

        return $schema;
    }

    function sync(array $schema) {
        if(!$this->exists()) {
            // Do nothing if table does not exist
            return;
        }

        $remoteSchema = $this->fetchSchema();

        // Remove columns which not exist in codebase
        foreach($remoteSchema as $attr => $settings) {
            if(!isset($schema[$attr])) {
                // attribute does not exist in codebase => drop field
                $this->dropField($attr);
            }    
        }

        // Add fields which not exist in db
        foreach($schema as $attr => $settings) {
            if(!isset($remoteSchema[$attr])) {
                // attribute does not exist in db => add field
                $this->addField($attr, $schema[$attr]);
            }
        }
    }

    /**
     * Find many entries
     * 
     * @param mixed $conditions
     * @return Set
     */
    function find(array $filter = [], array $options = []): Cursor 
    {
        $sql = "SELECT * FROM $this->name";
        
        if(count($filter) > 0) {
            $where = $this->buildWhere($filter);
            $sql .= " WHERE $where";
        }

        $stmt = $this->client->query($sql, $filter);
        
        return new FindResult($stmt);
    }

    function findFirst(array $conditions = []) 
    {
        $sql = "SELECT * FROM $this->name";

        if(count($conditions) > 0) {
            $where = $this->buildWhere($conditions);
            $sql .= " WHERE $where";
        }
        
        $sql .= " LIMIT 1";
        $stmt = $this->client->query($sql, $conditions);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insert(array $document, array $options = []) 
    {
        $fields = array_keys($document);
        $values = array_values($document);
        $placeholder = array_fill(0, count($values), '?');

        $sql = "INSERT INTO `$this->name` (" . implode(',', $fields). ") VALUES (" . implode(',', $placeholder). ")";

        $stmt = $this->client->query($sql, $values);

        return $this->client->lastInsertId();
    }

    function update(array $filter, array $data, array $options = []) 
    {
        $sql = "UPDATE $this->name";
        $params = $filter;

        if(count($data) > 0) {
            $mappedSetClause = array_map(function ($key) { 
                return "`" . $key . "` = :data_" . $key; 
            }, array_keys($data));
            
            $sql .= " SET " . implode(', ', $mappedSetClause);
            
            foreach($data as $k => $v) {
                $params['data_' . $k] = $v;
                
            }
        }

        if(count($filter) > 0) {
            $where = $this->buildWhere($filter);
            $sql .= " WHERE $where";
        }

        return $this->client->query($sql, $params);
    }

    function delete(array $filter, array $options = []) 
    {
        $where = $this->buildWhere($filter);
        $sql = "DELETE FROM $this->name WHERE $where";  

        $stmt = $this->client->query($sql, $filter);
        return $stmt;
    }

    private function buildWhere(array $conditions) 
    {
        $mappedConditions = array_map(function ($key) { 
            return "`". $key . "`= :" . $key; 
        }, array_keys($conditions));

        return implode(' AND ', $mappedConditions);
    }
}