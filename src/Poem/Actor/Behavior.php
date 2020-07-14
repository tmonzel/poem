<?php

namespace Poem\Actor;

use Poem\Actor;

abstract class Behavior {
    protected $config;
    protected $actor;

    function __construct(Actor $actor, $config = []) {
        $this->actor = $actor;
        $this->config = $config;
    }

    function registerAction(string $actionClass, callable $initializer = null) {
        $this->actor->addAction($actionClass, $initializer);
    }

    function initialize(ActionQuery $query) {

    }

    function prepareAction(Action $action) {
        
    }
}