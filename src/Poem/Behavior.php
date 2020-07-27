<?php

namespace Poem;

abstract class Behavior 
{
    protected $listeners = [];
    protected $config;

    function __construct($config = [])
    {   
        $this->config = $config;
        $this->initialize();
    }

    function shouldActOn($type, callable $callback) 
    {
        if(!isset($this->listeners[$type])) {
            $this->listeners[$type] = [];
        }

        $this->listeners[$type][] = $callback;
    }

    function dispatchEvent($subject, string $type, array $payload) 
    {
        if(!isset($this->listeners[$type])) {
            return;
        }

        foreach($this->listeners[$type] as $callback) {
            $callback($subject, $payload);
        }
    }

    abstract function initialize();
}
