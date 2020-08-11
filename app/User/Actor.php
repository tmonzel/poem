<?php

namespace User;

use Poem\Actor\BehaveAsResource;
use Poem\Auth\BehaveAsGuard;

class Actor extends \Poem\Auth\Actor 
{
    const Behaviors = [
        BehaveAsResource::class,
        BehaveAsGuard::class => [
            'except' => ['login', 'create', 'destroy']
        ]
    ];
}
