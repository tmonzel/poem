<?php

namespace Poem\Auth;

use Poem\Model;

/**
 * Predefined user model
 */
class User extends Model 
{
    const Schema = [
        'name' => 'string',
        'password' => 'string'
    ];

    /**
     * Registered model behaviors
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsUser::class
    ];
}