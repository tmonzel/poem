<?php

namespace Poem\Model;

use Poem\Model\Relationships\Relationship;

trait Relationships 
{
    /**
     * Lookup list with all available relationships classes
     * 
     * @static
     * @var array
     */
    static $availableRelationships = [
        Relationship::BELONGS_TO,
        Relationship::HAS_MANY,
        Relationship::HAS_ONE
    ];

    /**
     * Created relationship instances
     * 
     * @var Relationship[]
     */
    protected $relationships = [];

    /**
     * Adds a new relationship
     * 
     * @param string $relationshipClass
     * @param mixed $config
     * @return void
     */
    function addRelationship(string $relationshipClass, $config): void
    {
        if(array_search($relationshipClass, static::$availableRelationships) === false) {
            return;
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

    /**
     * Setting up a BelongsTo relationship
     * 
     * @param mixed $config
     * @return void
     */
    function belongsTo($config): void
    {
        $this->addRelationship(Relationship::BELONGS_TO, $config);
    }

    /**
     * Setting up a HasMany relationship
     * 
     * @param mixed $config
     * @return void
     */
    function hasMany($config): void
    {
        $this->addRelationship(Relationship::HAS_MANY, $config);
    }
}
