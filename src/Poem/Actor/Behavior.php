<?php

namespace Poem\Actor;

use Poem\Actor;

abstract class Behavior {
    protected $config;
    protected $actor;

    function __construct(Actor $actor, $config = []) {
        $this->actor = $actor;
        $this->config = $config;

        $this->initialize();
    }

    function registerAction(string $actionClass, callable $initializer = null) {
        $this->actor->addAction($actionClass, $initializer);
    }

    function initialize() {

    }

    function prepareAction(Action $action) {
        
    }
}