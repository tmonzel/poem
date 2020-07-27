<?php

namespace Poem\Auth;

use Poem\Actor;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Behavior;
use Poem\Auth\Accessor as AuthAccessor;

/**
 * Actor behaves as a Guard
 */
class BehaveAsGuard extends Behavior {
    use AuthAccessor;

    function initialize()
    {
        $this->shouldActOn(Actor::PREPARE_ACTION_EVENT, [$this, 'prepareAction']);
    }

    function prepareAction(Actor $actor, array $payload) 
    {
        $except = $permit = [];
        
        extract($this->config);
        extract($payload);

        if(array_search($actionType, $except) !== false) {
            return;
        }
        
        if(!static::Auth()->authorized()) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}
