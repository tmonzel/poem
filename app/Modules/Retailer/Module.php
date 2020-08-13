<?php

namespace Modules\Retailer;

use Poem\Actor;
use Poem\Model;
use Poem\Model\Relationships\Relationship;
use Poem\Module\Actable;
use Poem\Module\Storable;

/**
 * Retailer module
 */
class Module extends \Poem\Module 
{
    use Actable, Storable;
    
    static function getType(): string
    {
        return 'retailers';
    }

    function withModel(Model $retailers) 
    {
        $retailers->addRelationship(
            Relationship::HAS_MANY,
            'markets'
        );
    }

    function withActor(Actor $actor)
    {
        $actor->bind(Actor::RESOURCE_ACTIONS);
        
        // TODO: Add guard behavior
    }

    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
    const Schema = [
        'id' => 'pk',
        'name' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];
}
