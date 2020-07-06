<?php

namespace Poem\Actor;

abstract class Behavior {
    protected $config;

    function __construct($config = []) {
        $this->config = $config;
    }

    function prepareActions(ActionDispatcher $actions) {

    }
}