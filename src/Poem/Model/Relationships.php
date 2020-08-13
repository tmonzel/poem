<?php

namespace Poem\Model;

use Poem\Model\Relationships\BelongsToRelationship;
use Poem\Model\Relationships\HasManyRelationship;
use Poem\Model\Relationships\HasOneRelationship;
use Poem\Model\Relationships\Relationship;

trait Relationships 
{
    static $availableRelationships = [
        Relationship::BELONGS_TO,
        Relationship::HAS_MANY,
        Relationship::HAS_ONE
    ];

    /**
     * 
     * @var Relationship[]
     */
    protected $relationships = [];

    function addRelationship(string $relationshipClass, $config) 
    {
        if(array_search($relationshipClass, static::$availableRelationships) === false) {
            return false;
        }

        if(is_string($config)) {
            $config = [$config];
        }

        foreach($config as $accessor => $rel) {
            if(is_numeric($accessor)) {
                $accessor = $rel;
                $rel = ['target' => $accessor];
            } elseif(is_string($rel)) {
                $rel = ['target' => $rel];
            }

            $this->relationships[$accessor] = new $relationshipClass($this, $rel);
        }
    }

    function hasRelationship($name) 
    {
        return isset($this->relationships[$name]);
    }

    function getRelationship($name): ?Relationship 
    {
        if($this->hasRelationship($name)) {
            return $this->relationships[$name];
        }

        return null;
    }
}
