<?php

namespace Modules\User;

use Poem\Actor\BehaveAsResource;
use Poem\Auth\BehaveAsGuard;

class Actor extends \Poem\Auth\Actor 
{
    /**
     * Applied user behaviors
     * 
     * @var array 
     */
    const Behaviors = [
        BehaveAsResource::class,
        BehaveAsGuard::class => [
            'except' => [
                'login', 
                'create', 
                'destroy'
            ]
        ]
    ];
}
