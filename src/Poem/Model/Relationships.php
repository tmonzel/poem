<?php

namespace Poem\Model;

use Poem\Model\Relationships\BelongsToRelationship;
use Poem\Model\Relationships\HasManyRelationship;
use Poem\Model\Relationships\HasOneRelationship;
use Poem\Model\Relationships\Relationship;

trait Relationships {
    static $relationships = [];
    static $relationshipTypes = [
        'HasMany' => HasManyRelationship::class,
        'HasOne' => HasOneRelationship::class,
        'BelongsTo' => BelongsToRelationship::class
    ];

    protected $relations = [];

    static function initializeRelationships() {
        $calledClass = get_called_class();

        // Do not initialize twice for this model
        if(isset(static::$relationships[$calledClass])) {
            return;
        }

        static::$relationships[$calledClass] = [];

        foreach(static::$relationshipTypes as $type => $relationshipClass) {
            $relationshipDef = $calledClass . '::' . $type;

            if(defined($relationshipDef)) {
                $relConfig = constant($relationshipDef);

                foreach($relConfig as $name => $subject) {
                    static::$relationships[$calledClass][$name] = new $relationshipClass($subject, $name);
                }
            }
        }
    }

    function getRelationship($name): ?Relationship {
        $calledClass = get_called_class();
    
        if(isset(static::$relationships[$calledClass][$name])) {
            return static::$relationships[$calledClass][$name];
        }

        return null;
    }

    function getConnectedRelationship($name) {
        $relationship = $this->getRelationship($name);

        if($relationship) {
            $relationship->connect($this);
        }

        return $relationship;
    }

    function hasRelation($name) {
        return isset($this->relations[$name]);
    }

    function setRelation($name, $relation) {
        $this->relations[$name] = $relation;
    }

    function toRelatedData() {
        $calledClass = get_called_class();

        return ['id' => (int)$this->id, 'type' => $calledClass::Type];
    }
}