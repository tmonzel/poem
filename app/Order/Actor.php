<?php

namespace Order  {

    use Poem\Actor\BehaveAsResource;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            BehaveAsResource::class,
        ];

        function prepareEvents($events) {
            $events->change('state', function() {
                // Do something if state is changed from cart to ordered
                // can be dispatched by patch/put methods
            });
        }
    }
}