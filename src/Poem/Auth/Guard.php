<?php

namespace Poem\Auth;

use Poem\Actor\Action;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Guard behavior
 */
class Guard extends Behavior {
    function prepareAction(Action $action) {
        $except = [];

        extract($this->config);

        if(array_search($action->getType(), $except) === false) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}