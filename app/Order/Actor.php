<?php

namespace Order;

use Poem\Actor\BehaveAsResource;

class Actor extends \Poem\Actor 
{
    /**
     * Order actor type definition
     * 
     * @var string
     */
    const Type = 'orders';

    /**
     * Registered order actor behaviors
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class
    ];

    /**
     * @TODO: React on events
     * Maybe this is more model related and must be moved to collection or document
     */
    function prepareEvents($events) {
        $events->change('state', function() {
            // Do something if state is changed from cart to ordered
            // can be dispatched by patch/put methods
        });
    }
}
