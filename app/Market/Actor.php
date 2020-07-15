<?php

namespace Market  {
    use Poem\Actor\Behaviors\ResourceBehavior;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
        ];
    }
}