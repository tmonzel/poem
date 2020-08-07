<?php

namespace User;

use Poem\Auth\User;

class Collection extends \Poem\Model\Collection 
{
    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
    const Schema = User::Schema;

    /**
     * Registered model behaviors
     * 
     * @var array
     */
    const Behaviors = User::Behaviors;

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
