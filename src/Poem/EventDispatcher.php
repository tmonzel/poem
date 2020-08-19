<?php

namespace Poem;

/**
 * Just another event dispatcher interface
 */
trait EventDispatcher 
{   
    /**
     * Added listener callbacks
     * 
     * @var array
     */
    protected $listeners = [];

    /**
     * Adds a new listener callback for a given type.
     * 
     * @param string $type
     * @param callable $listener
     * @return void
     */
    function addEventListener(string $type, callable $listener): void
    {
        if(!isset($this->listeners[$type])) {
            $this->listeners[$type] = [];
        }

        $this->listeners[$type][] = $listener;
    }

    /**
     * Triggers an event with an optional payload
     * 
     * @param string $type
     * @param mixed $payload
     * @return void
     */
    function dispatchEvent(string $type, $payload = null): void 
    {
        if(!isset($this->listeners[$type])) {
            return;
        }

        foreach($this->listeners[$type] as $listener) {
            call_user_func($listener, $payload, $this);
        }
    }
}
