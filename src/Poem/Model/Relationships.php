<?php

namespace Poem\Model;

use Poem\Model\Relationships\BelongsToRelationship;
use Poem\Model\Relationships\HasManyRelationship;
use Poem\Model\Relationships\HasOneRelationship;
use Poem\Model\Relationships\Relationship;

trait Relationships 
{
    static $relationshipTypes = [
        'HasMany' => HasManyRelationship::class,
        'HasOne' => HasOneRelationship::class,
        'BelongsTo' => BelongsToRelationship::class
    ];

    /**
     * 
     * @var Relationship[]
     */
    protected $relationships = [];

    function initializeRelationships() 
    {
        $calledClass = get_called_class();

        foreach(static::$relationshipTypes as $type => $relationshipClass) {
            $relationshipConstant = $calledClass . '::' . $type;

            if(defined($relationshipConstant)) {
                $this->addRelationship($type, constant($relationshipConstant));
            }
        }
    }

    function addRelationship($type, $config) 
    {
        if(!isset(static::$relationshipTypes[$type])) {
            return false;
        }

        if(is_string($config)) {
            $config = [$config];
        }

        $relationshipClass = static::$relationshipTypes[$type];

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
