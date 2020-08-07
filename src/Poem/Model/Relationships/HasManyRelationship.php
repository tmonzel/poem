<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Collection;

/**
 * Proxy for has many relations
 */
class HasManyRelationship extends Relationship 
{
    function attachTo(Collection $collection, Statement $statement) 
    {
        $query = $this->find();
        $foreignKey = $collection->foreignKey();
        $resultMap = [];

        foreach($query as $document) {
            $foreignId = $document->{$foreignKey};

            if(!isset($resultMap[$foreignId])) {
                $resultMap[$foreignId] = [];
            }

            $resultMap[$foreignId][] = $document;
        }

        $target = $this->options['target'];

        $statement->addMapper(function($row) use($resultMap, $target) {
            $row[$target] = isset($resultMap[$row['id']]) ? $resultMap[$row['id']] : [];
            return $row;
        });
    }
}