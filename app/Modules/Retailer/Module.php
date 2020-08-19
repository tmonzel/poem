<?php

namespace Modules\Retailer;

use Poem\Model;
use Poem\Model\Relationships\Relationship;
use Poem\Module\Actable;
use Poem\Module\Storable;

/**
 * Retailer module
 */
class Module extends \Module 
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

        // Set the data schema
        // Only needed for migrations
        $retailers->setSchema([
            'id' => 'pk',
            'name' => 'string',
            'created_at' => 'date',
            'updated_at' => 'date'
        ]);
    }

    function withActor(\Actor $actor) 
    {
        $actor->bind(\Actor::RESOURCE_ACTIONS);
        $actor->guardActions();
    }
}
