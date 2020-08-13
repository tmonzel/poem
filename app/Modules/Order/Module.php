<?php

namespace Modules\Order;

use Poem\Module\Actable;
use Poem\Module\Storable;

class Module extends \Poem\Module 
{
    use Actable, Storable;

    static function getType(): string
    {
        return 'orders';
    }

    function withActor(Actor $actor) 
    {
        $actor->bind(Actor::RESOURCE_ACTIONS);
    }

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
