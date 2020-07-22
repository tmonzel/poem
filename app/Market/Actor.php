<?php

namespace Market;

use Poem\Actor\Behaviors\ResourceBehavior;

class Actor extends \Poem\Actor {

    /**
     * Registered behaviors for this actor
     * 
     * @var array
     */
    const Behaviors = [
        ResourceBehavior::class,
    ];
}
