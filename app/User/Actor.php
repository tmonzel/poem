<?php

namespace User;

use Poem\Actor\Actions\{ 
    LoginAction 
};
use Poem\Actor\BehaveAsResource;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth\BehaveAsGuard;

class Actor extends \Poem\Actor 
{

    /**
     * Initializes with all the resource actions
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class,
        BehaveAsGuard::class => [
            'except' => ['login', 'create']
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
}
