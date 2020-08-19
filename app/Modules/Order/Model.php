<?php

namespace Modules\Order;

use Poem\Model\Relationships\Relationship;

class Model extends \Poem\Model 
{
    /**
     * Initializes the order model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->setSchema([
            'id' => 'pk',
            'state' => 'string'
        ]);

        $this->addRelationship(
            Relationship::HAS_MANY,
            ['items' => 'order_items']
        );
    }
}