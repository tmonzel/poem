<?php

namespace User;

use Poem\Actor\Actions\{ 
    LoginAction 
};
use Poem\Actor\BehaveAsResource;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth\BehaveAsGuard;
use Poem\Auth\Accessor as AuthAccessor;

class Actor extends \Poem\Actor 
{
    use AuthAccessor;

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
            if(!static::Auth()->authorized()) {
                throw new UnauthorizedException('No authorized user found');
            }

            return static::Auth()->user();
        });
    }
}
