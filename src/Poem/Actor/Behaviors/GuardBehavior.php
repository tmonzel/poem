<?php

namespace Poem\Actor\Behaviors;

use Poem\Actor\ActionQuery;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Guard behavior
 */
class GuardBehavior extends Behavior 
{
    function initialize(ActionQuery $query) 
    {
        $except = [];

        extract($this->config);

        if(array_search($query->getType(), $except) !== false) {
            return;
        }
        
        if(!$query->auth || !$query->auth->authorized()) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}