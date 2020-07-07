<?php

namespace Market  {
    use Poem\Actor\ResourceBehavior;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
        ];
    }
}