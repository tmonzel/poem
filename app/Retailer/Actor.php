<?php

namespace Retailer;

use Poem\Actor\BehaveAsResource;
use Poem\Auth\BehaveAsGuard;

class Actor extends \Poem\Actor 
{
    /**
     * Retailer type definition
     * 
     * @var string
     */
    const Type = 'retailers';

    /**
     * Registered action behaviors
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class,
        BehaveAsGuard::class => [
            'permit' => [
                '*' => ['admin'],
                'find' => ['admin']
            ],

            'except' => [
                'create', 'find'
            ]
        ]
    ];

    function create($action) {
        // modify create action
    }

    function find($action) {
        // modify find action
    }
}
