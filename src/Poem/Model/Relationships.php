<?php

namespace Poem\Model;

use Poem\Model\Relationships\BelongsToRelationship;
use Poem\Model\Relationships\HasManyRelationship;
use Poem\Model\Relationships\HasOneRelationship;

trait Relationships {
    static $relationships = [];
    static $relationshipTypes = [
        'HasMany' => HasManyRelationship::class,
        'HasOne' => HasOneRelationship::class,
        'BelongsTo' => BelongsToRelationship::class
    ];

    protected $relations = [];

    function buildRelationship($name) {
        $calledClass = get_called_class();
        $relationship = null;

        foreach(static::$relationshipTypes as $type => $relationshipClass) {
            if(defined($calledClass . '::' . $type)) {
                $relationship = new $relationshipClass($calledClass, $name);
                break;
            }
        }

        return $relationship;
    }

    function getRelationship($name) {
        $calledClass = get_called_class();
    
        if(isset(static::$relationships[$calledClass][$name])) {
            return static::$relationships[$calledClass][$name];
        }

        // Create new relationship object
        $relationship = $this->buildRelationship($name);
        
        if($relationship) {
            return static::$relationships[$calledClass][$name] = $relationship;
        }

        return null;
    }

    function hasRelation($name) {
        return isset($this->relations[$name]);
    }

    function setRelation($name, $relation) {
        $this->relations[$name] = $relation;
    }

    function connectRelationship($name) {
        $relationship = $this->getRelationship($name);

        // setRelation in connect to query and set results
        return $relationship->connect($this);
    }
}