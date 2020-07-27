<?php

namespace Product  {

    use Poem\Actor\BehaveAsResource;
    use Poem\Auth\BehaveAsGuard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            BehaveAsResource::class,
            BehaveAsGuard::class => [
                'except' => ['find']
            ]
        ];
    }
}