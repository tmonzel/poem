<?php

namespace Product  {

    use Poem\Actor\Behaviors\GuardBehavior;
    use Poem\Actor\Behaviors\ResourceBehavior;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            GuardBehavior::class => [
                'role' => 'admin',
                'except' => ['find']
            ]
        ];
    }
}