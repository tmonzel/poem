<?php

namespace Poem\Actor\Behaviors;

use Poem\Actor;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Guard behavior
 */
class GuardBehavior extends Behavior 
{
    function initialize(Actor $actor, string $actionType, array $payload = []) 
    {
        $except = $permit = [];
        $auth = $actor->getAuth();

        extract($this->config);

        if(array_search($actionType, $except) !== false) {
            return;
        }
        
        if(!$auth->authorized()) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}