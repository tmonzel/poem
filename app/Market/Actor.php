<?php

namespace Market;

use Poem\Actor\BehaveAsResource;

class Actor extends \Poem\Actor {

    /**
     * Registered behaviors for this actor
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class,
    ];
}
