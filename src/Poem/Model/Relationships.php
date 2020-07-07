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

    function buildRelationship($name) {
        $calledClass = get_called_class();
        $relationship = null;

        foreach(static::$relationshipTypes as $type => $relationshipClass) {
            if(defined($calledClass . '::' + $type)) {
                

                $relationship = new $relationshipClass();
                break;
            }
        }

        return $relationship;
    }

    function getRelationship($name) {
        if(isset(static::$relationships[$name])) {
            return static::$relationships[$name];
        }

        // Create new relationship object
        $relationship = $this->buildRelationship($name);

        if($relationship) {
            return static::$relationships[$name] = $relationship;
        }

        return null;
    }
}