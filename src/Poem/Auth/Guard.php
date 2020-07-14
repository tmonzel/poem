<?php

namespace Poem\Auth;

use Poem\Actor\ActionQuery;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Guard behavior
 */
class Guard extends Behavior 
{
    function initialize(ActionQuery $query) 
    {
        $except = [];

        extract($this->config);

        if(array_search($query->getType(), $except) === false) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}