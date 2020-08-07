<?php

namespace Poem\Model;

use Poem\Model\Relationships\BelongsToRelationship;
use Poem\Model\Relationships\HasManyRelationship;
use Poem\Model\Relationships\HasOneRelationship;
use Poem\Model\Relationships\Relationship;

trait Relationships 
{
    static $relationships = [];
    static $relationshipTypes = [
        'HasMany' => HasManyRelationship::class,
        'HasOne' => HasOneRelationship::class,
        'BelongsTo' => BelongsToRelationship::class
    ];

    protected $relations = [];

    static function initializeRelationships() 
    {
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

                foreach($relConfig as $accessor => $config) {
                    if(is_numeric($accessor)) {
                        $accessor = $config;
                        $config = ['target' => $accessor];
                    }

                    static::$relationships[$calledClass][$accessor] = new $relationshipClass($config);
                }
            }
        }
    }

    function hasRelationship($name) 
    {
        return isset(static::$relationships[static::class][$name]);
    }

    function getRelationship($name): ?Relationship 
    {
        if($this->hasRelationship($name)) {
            return static::$relationships[static::class][$name];
        }

        return null;
    }
}
