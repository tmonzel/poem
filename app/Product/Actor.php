<?php

namespace Product  {
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\AuthGuard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            AuthGuard::class => [
                'role' => 'admin',
                'except' => ['find']
            ]
        ];
    }
}