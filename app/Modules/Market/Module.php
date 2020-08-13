<?php

namespace Modules\Market;

use Poem\Actor;
use Poem\Model;
use Poem\Model\Relationships\Relationship;
use Poem\Module\Actable;
use Poem\Module\Storable;

class Module extends \Poem\Module 
{
    use Actable, Storable;

    static function getType(): string 
    {
        return 'markets';
    }
    
    function withModel(Model $markets)
    {
        $markets->addRelationship(
            Relationship::BELONGS_TO, 
            ['retailer' => 'retailers']
        );
    }

    function withActor(Actor $actor)
    {
        $actor->bind(Actor::RESOURCE_ACTIONS);
    }
}
