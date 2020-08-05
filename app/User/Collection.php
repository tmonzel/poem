<?php

namespace User;

use Poem\Auth\BehaveAsUser;
use Poem\Model\Validateable;

class Collection extends \Poem\Model\Collection 
{
    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
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

    /**
     * User validations
     * 
     * @return array
     */
    function validations(): array
    {
        return [
            'name' => 'required',
            'password' => 'required'
        ];
    }
}
