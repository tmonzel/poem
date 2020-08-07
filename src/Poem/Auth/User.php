<?php

namespace Poem\Auth;

use Poem\Model\Collection;

class User extends Collection {
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