<?php

namespace User;

use Poem\Actor\Actions\{
    CreateAction, 
    LoginAction 
};
use Poem\Actor\Behaviors\GuardBehavior;
use Poem\Actor\Behaviors\ResourceBehavior;
use Poem\Actor\Exceptions\UnauthorizedException;

class Actor extends \Poem\Actor 
{

    /**
     * Initializes with all the resource actions
     * 
     * @var array
     */
    const Behaviors = [
        ResourceBehavior::class,
        GuardBehavior::class => [
            'except' => ['find']
        ]
    ];

    /**
     * Prepare or add additional actions
     * 
     */
    function initialize(string $actionType, array $payload = [])
    {
        $this->registerAction(LoginAction::class);
        $this->registerAction('me', function($payload) {
            if(!$this->auth->authorized()) {
                throw new UnauthorizedException('No authorized user found');
            }

            return $this->auth->user();
        });
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
