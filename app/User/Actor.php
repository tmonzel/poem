<?php

namespace User;

use Poem\Actor\ActionQuery;
use Poem\Actor\Actions\{
    CreateAction, 
    LoginAction 
};
use Poem\Actor\Behaviors\ResourceBehavior;

class Actor extends \Poem\Actor {

    /**
     * Behaves as a resource
     * 
     * @var array
     */
    const Behaviors = [
        ResourceBehavior::class,
    ];

    /**
     * Prepare or add additional actions
     * 
     */
    function initialize(ActionQuery $query) 
    {
        $this->registerAction(LoginAction::class);
    }

    /**
     * Modify create user action
     * 
     * @param CreateAction $action
     */
    function create(CreateAction $action)
    {
        $action->mapAttribute('password', function($password) {
            return password_hash($password, PASSWORD_ARGON2I);
        });
    }
}
