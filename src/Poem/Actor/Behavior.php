<?php

namespace Poem\Actor;

use Poem\Actor;

abstract class Behavior {
    protected $config;

    function __construct($config = []) {
        $this->config = $config;
    }

    function initialize(Actor $actor, string $actionType, array $payload = []) {

    }

    function prepareAction(Action $action) {
        
    }
}