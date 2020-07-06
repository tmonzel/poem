<?php

namespace User;

use Poem\Actor\ActionDispatcher;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\ResourceBehavior;
use Poem\Auth\Actions\LoginAction;

class Actor extends \Poem\Actor {
    const Behaviors = [
        ResourceBehavior::class
    ];

    /**
     * @param ActionDispatcher $actions
     */
    function prepareActions(ActionDispatcher $actions) 
    {
        $actions->add(LoginAction::class);

        // Create user action
        $actions->add(CreateAction::class, function(CreateAction $create) {
            $create->mapAttribute('password', function($password) {
                return password_hash($password, PASSWORD_ARGON2I);
            });
        });
    }
}
