<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Collection;

/**
 * Proxy for belongs to relations
 */
class BelongsToRelationship extends Relationship 
{
    function attachTo(Collection $collection, Statement $statement) 
    {
        $query = $this->find();
        $foreignKey = $this->getForeignKey();
        $resultMap = [];
        $target = $this->getTargetModel()->getName();

        foreach($query as $document) {
            $document->setFormat(['id', 'type']);
            $resultMap[$document->id] = $document;
        }

        $statement->addMapper(function($row) use($target, $resultMap, $foreignKey) {
            $row[$target] = $resultMap[$row[$foreignKey]];
            unset($row[$foreignKey]);
            return $row;
        });
    }
}