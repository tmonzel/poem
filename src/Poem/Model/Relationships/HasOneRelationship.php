<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Collection;
use stdClass;

/**
 * Proxy for has one relations
 */
class HasOneRelationship extends Relationship 
{
    function attachTo(Collection $collection, Statement $statement) 
    {
        $query = $this->find();
        $foreignKey = $collection->foreignKey();
        $target = $this->getTargetModel()->getName();
        $resultMap = [];

        foreach($query as $document) {
            $foreignId = $document->{$foreignKey};
            
            if(isset($resultMap[$foreignId])) {
                continue;
            }

            $document->setFormat(['id', 'type']);

            $resultMap[$foreignId] = $document;
        }

        $statement->addMapper(function($row) use($resultMap, $target) {
            if(isset($resultMap[$row['id']])) {
                $row[$target] = $resultMap[$row['id']];
            } else {
                $row[$target] = new stdClass;
            }

            return $row;
        });
    }
}