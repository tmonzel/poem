<?php

namespace User;

use Poem\Actor\ActionQuery;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\ResourceBehavior;
use Poem\Auth\Actions\LoginAction;

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
        $this->addAction(LoginAction::class);
    }

    function login(LoginAction $action) 
    {
        
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
