<?php

namespace Poem\Actor\Behaviors;

use Poem\Actor;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth\Accessor as AuthAccessor;
/**
 * Protect actions from being accessed
 */
class GuardBehavior extends Behavior 
{
    use AuthAccessor;
    
    function initialize(Actor $actor, string $actionType, array $payload = []) 
    {
        $except = $permit = [];

        extract($this->config);

        if(array_search($actionType, $except) !== false) {
            return;
        }
        
        if(!static::Auth()->authorized()) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}