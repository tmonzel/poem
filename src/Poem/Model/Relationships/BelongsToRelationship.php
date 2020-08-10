<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Collection;
use Poem\Model\Document;
use stdClass;

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
        $target = $this->getName();

        foreach($query as $document) {
            $resultMap[$document->id] = $document;
        }

        $statement->addMapper(function($row) use($target, $resultMap, $foreignKey) {
            if(isset($resultMap[$row[$foreignKey]])) {
                $row[$target] = $resultMap[$row[$foreignKey]];
            } else {
                $row[$target] = new stdClass;
            }

            unset($row[$foreignKey]);
            return $row;
        });
    }

    function applyTo(Document $document, $data)
    {
        $propertyName = $this->getName();
        
        if($data === null) {
            // Set related document to null (will be removed on save)
            $document->{$propertyName} = null;
            return;
        }
        
        if(isset($data['id'])) {
            $document->{$propertyName} = $this->pick($data['id']);
        }
    }

    function saveTo(Document $document): void 
    {
        $propertyName = $this->getName();
        
        if(!$document->has($propertyName) || !$document->isDirty($propertyName)) {
            return;
        }

        $related = $document->{$propertyName};

        if($related instanceof Document) {
            $relatedId = $related->id;
        } else {
            $relatedId = null;
        }

        $this->model->accessAdapter()->update([
            'id' => $document->id
        ], [$this->getForeignKey() => $relatedId]);
    }
}