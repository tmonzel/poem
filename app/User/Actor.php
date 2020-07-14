<?php

namespace User;

use Poem\Actor\ActionQuery;
use Poem\Actor\ResourceBehavior;
use Poem\Auth\Actions\LoginAction;
use Poem\Auth\AuthBehavior;

class Actor extends \Poem\Actor {

    /**
     * Behaves as a resource
     * 
     * @var array
     */
    const Behaviors = [
        ResourceBehavior::class,
        AuthBehavior::class
    ];

    /**
     * Prepare or add additional actions
     * 
     */
    function initialize(ActionQuery $query) 
    {
        $this->addAction(LoginAction::class);
    }
}
