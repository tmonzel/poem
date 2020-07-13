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

    function prepareActions(ActionDispatcher $actions) {
        
    }
}