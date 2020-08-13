<?php

namespace Modules\Market;

use Poem\Actor\BehaveAsResource;
use Poem\Model;
use Poem\Model\Relationships\Relationship;

class Actor extends \Poem\Actor 
{
    /**
     * Market actor type definition
     * 
     * @var string
     */
    const Type = 'markets';

    /**
     * Registered market actor behaviors
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class,
    ];

    /**
     * Only invoked if model accessed
     * 
     */
    static function withModel(Model $model)
    {
        $model->addRelationship(
            Relationship::BELONGS_TO, 
            ['retailer' => 'retailers']
        );
    }

    static function withActor()
    {

    }
}
