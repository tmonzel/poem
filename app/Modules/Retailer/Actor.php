<?php

namespace Modules\Retailer;

use Poem\Actor\BehaveAsResource;
use Poem\Auth\BehaveAsGuard;

/**
 * Retailer endpoint actor
 */
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
                'create', 'find', 'update'
            ]
        ]
    ];

    /**
     * Relationships which will be added to the model instance
     * 
     * @var array
     */
    const Relationships = [
        'HasMany' => 'markets'
    ];

    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
    const Schema = [
        'id' => 'pk',
        'name' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];

    function create($action) {
        // modify create action
    }

    function find($action) {
        // modify find action
    }
}
