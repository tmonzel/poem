<?php

namespace Poem\Auth;

use Poem\Actor\Exceptions\UnauthorizedException;

class Actor extends \Poem\Actor 
{
    use Accessor;

    /**
     * User actor type definition
     * 
     * @var string
     */
    const Type = 'users';

    /**
     * Register additional actions
     * 
     * @return void
     */
    function initialize(): void
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