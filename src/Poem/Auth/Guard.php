<?php

namespace Poem\Auth;

use Poem\Actor\Action;
use Poem\Actor\ActionDispatcher;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Actor behavior trait
 */
class Guard extends Behavior {
    function beforeAction(Action $action) {
        $except = [];

        extract($this->config);

        if(array_search($action->getType(), $except) === false) {
            throw new UnauthorizedException('Action not allowed');
        }
    }

    function prepareActions(ActionDispatcher $actions) {
        $actions->addListener('before', [$this, 'beforeAction']);
    }
}