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
                $relConfig = constant($relationshipConstant);

                foreach($relConfig as $accessor => $config) {
                    if(is_numeric($accessor)) {
                        $accessor = $config;
                        $config = ['target' => $accessor];
                    } elseif(is_string($config)) {
                        $config = ['target' => $config];
                    }

                    $this->relationships[$accessor] = new $relationshipClass($this, $config);
                }
            }
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
