<?php

namespace Poem\Auth;

use Poem\Actor\ActionDispatcher;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;

/**
 * Actor behavior trait
 */
class Guard extends Behavior {
    function beforeAction($action, $request) {
        $actionClass = get_class($action);
        $allowActions = [];

        extract($this->config);

        if(array_search($actionClass, $allowActions) === false) {
            throw new UnauthorizedException('');
        }
    }

    function prepareActions(ActionDispatcher $actions) {
        $actions->addListener('before', [$this, 'beforeAction']);
    }
}