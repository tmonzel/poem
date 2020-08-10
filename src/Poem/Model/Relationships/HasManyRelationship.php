<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Collection;
use Poem\Model\Document;

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

    function applyTo(Document $document, $data)
    {
        $propertyName = $this->getType();
        
        if($data === null) {
            // Set related documents to null (will be removed on save)
            $document->{$propertyName} = null;
            return;
        }
        
        if(is_array($data) && !empty($data)) {
            $ids = array_map(function($val) {
                return $val['id'];
            }, $data);

            $query = $this->pickMany($ids);

            $related = array_filter($query->all(), function($document) use($ids) {
                return array_search($document->id, $ids) !== false;
            });
            
            $document->{$propertyName} = $related;
        }
    }

    function saveTo(Document $document): void 
    {
        $propertyName = $this->getType();
        
        if(!$document->has($propertyName) || !$document->isDirty($propertyName)) {
            return;
        }

        $related = $document->{$propertyName};
        $adapter = $this->getTargetModel()->accessAdapter();
        $foreignKey = $this->model->foreignKey();

        if(is_array($related)) {
            // Syncronize
            // Set all to null initially
            $adapter->update([$foreignKey => $document->id], [$foreignKey => null]);

            foreach($related as $doc) {
                $adapter->update(['id' => $doc->id], [$foreignKey => $document->id]);
            }

        } else {
            // Remove all related
            $adapter->update([$foreignKey => $document->id], [$foreignKey => null]);
        }
    }
}
