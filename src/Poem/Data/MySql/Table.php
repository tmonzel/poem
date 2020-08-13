<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\CollectionAdapter;
use Poem\Data\Statement;

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

    /**
     * Ensure this table exists
     * 
     * @return bool
     */
    function exists(): bool 
    {
        return $this->client->query(
            "SHOW TABLES LIKE '$this->name'"
        )->rowCount() > 0;
    }

    /**
     * Drop table field
     * 
     * @param string $name
     * @return PDOStatement
     */
    function dropField(string $name) 
    {
        return $this->client->query(
            "ALTER TABLE $this->name DROP COLUMN $name"
        );
    }

    /**
     * Add a new table field
     * 
     * @param string $name
     * @param string $type
     * @return PDOStatement
     */
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

    /**
     * Migrate this table based on the given schema
     * 
     * @param array $schema
     * @return void
     */
    function migrate(array $schema): void 
    {
        if(!$this->exists()) {
            // Create new
            $this->client->createTable($this->name, $schema);
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
     * 
     * @return int
     */
    function count(): int
    {
        return (int)$this->client->query('SELECT count(*) FROM ' . $this->name)->fetchColumn();
    }

    /**
     * Find many entries
     * 
     * @param array $filter
     * @param array $options
     * @return Statement
     */
    function find(array $filter = [], array $options = []): Statement 
    {
        $sql = "SELECT * FROM $this->name";
        $params = [];
        $limit = null;

        extract($options);

        if(isset($join)) {
            if($join['type'] === 'left') {
                $sql .= " LEFT JOIN " . $join['target'] . " ON (" . $join['on'] . ")";
            }
        }

        $this->appendWhereClause($sql, $params, $filter);
        $this->appendLimitClause($sql, $params, $limit);

        $stmt = $this->client->query($sql, $params);
        
        return new FindResult($this, $stmt, $options);
    }

    function insert(array $data, array $options = []) 
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholder = array_fill(0, count($values), '?');

        $sql = "INSERT INTO `$this->name` (" . implode(',', $fields). ") VALUES (" . implode(',', $placeholder). ")";

        $this->client->query($sql, $values);

        return $this->client->lastInsertId();
    }

    function update(array $filter, array $data, array $options = []) 
    {
        $sql = "UPDATE $this->name";
        $params = [];
        $limit = null;

        if(count($data) > 0) {
            $mappedSetClause = array_map(function ($key) { 
                return "`" . $key . "` = ?"; 
            }, array_keys($data));
            
            $sql .= " SET " . implode(', ', $mappedSetClause);
            $params = array_values($data);
        }

        extract($options);

        $this->appendWhereClause($sql, $params, $filter);
        $this->appendLimitClause($sql, $params, $limit);
        
        return $this->client->query($sql, $params);
    }

    function delete(array $filter, array $options = []) 
    {
        $sql = "DELETE FROM $this->name";
        $params = [];
        $limit = null;

        extract($options);

        $this->appendWhereClause($sql, $params, $filter);
        $this->appendLimitClause($sql, $params, $limit);

        return $this->client->query($sql, $params);
    }

    function truncate(): void 
    {
        $this->client->truncate($this->name);
    }

    private function appendLimitClause(&$sql, &$params, $limit) 
    {
        if(isset($limit)) {
            $sql .= " LIMIT $limit";
        }
    }

    private function appendWhereClause(&$sql, &$params, $where) 
    {
        if(is_array($where) && count($where) > 0) {
            $conditions = [];

            foreach($where as $key => $value) {
                if(is_array($value)) {
                    $placeholder = array_fill(0, count($value), '?');
                    $conditions[] = "`". $key . "` IN (" . implode(',', $placeholder) . ")";
                    array_push($params, ...$value);
                } else {
                    $conditions[] = "`". $key . "`= ?";
                    $params[] = $value;
                }
            }

            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
    }
}
