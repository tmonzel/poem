<?php

namespace Poem\Data\MySql;

use PDO;
use Poem\Data\Collection;
use Poem\Set;

class Table extends Collection {
    
    /**
     * @var PDO
     */
    protected $connection;

    function __construct($name, PDO $connection) {
        parent::__construct($name);
        $this->connection = $connection;
    }

    function findMany($conditions = []): Set {
        $sql = "SELECT * FROM $this->name";
        
        if(count($conditions) > 0) {
            $where = $this->buildWhere($conditions);
            $sql .= " WHERE $where";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($conditions);
        
        return new ResultSet($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    function findFirst(array $conditions = []) {
        $sql = "SELECT * FROM $this->name";

        if(count($conditions) > 0) {
            $where = $this->buildWhere($conditions);
            $sql .= " WHERE $where";
        }
        
        $sql .= " LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($conditions);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insert(array $document) {
        $fields = array_keys($document);
        $values = array_values($document);
        $placeholder = array_fill(0, count($values), '?');

        $sql = "INSERT INTO `$this->name` (" . implode(',', $fields). ") VALUES (" . implode(',', $placeholder). ")";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);

        return $this->connection->lastInsertId();
    }

    function updateFirst(array $data, array $conditions = []) {
        $sql = "UPDATE $this->name";
        $params = $conditions;

        if(count($data) > 0) {
            $mappedSetClause = array_map(function ($key) { 
                return $key . " = :data_" . $key; 
            }, array_keys($data));
            
            $sql .= " SET " . implode(',', $mappedSetClause);
            
            foreach($data as $k => $v) {
                $params['data_' . $k] = $v;
                
            }
        }

        if(count($conditions) > 0) {
            $where = $this->buildWhere($conditions);
            $sql .= " WHERE $where";
        }
        
        $sql .= " LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function deleteFirst(array $conditions = []) {
        $where = $this->buildWhere($conditions);
        $sql = "DELETE FROM $this->name WHERE $where";  

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($conditions);
    }

    private function buildWhere(array $conditions) {
        $mappedConditions = array_map(function ($key) { 
            return "`". $key . "`= :" . $key; 
        }, array_keys($conditions));

        return implode(' AND ', $mappedConditions);
    }
}