<?php

namespace Poem\Actor;

use Exception;
use Poem\Actor;

class Worker 
{
    /**
     * Registered module classes
     * 
     * @var array
     */
    protected $registry = [];

    /**
     * Created actor instances
     * 
     * @var array
     */
    protected $actors = [];

    /**
     * Registers an actor
     * 
     * @param string $name
     * @param mixed $actorClass
     * @return void
     */
    function register(string $name, $actorClass): void
    {
        $this->registry[$name] = $actorClass;
    }

    /**
     * Access a given actor by name
     * 
     * @param string $name
     * @return Actor
     */
    function access(string $name): Actor 
    {
        if(isset($this->actors[$name])) {
            return $this->actors[$name];
        }

        if(!isset($this->registry[$name])) {
            throw new Exception("Actor `$name` not registered");
        }

        if(is_callable($this->registry[$name])) {
            $actor = call_user_func($this->registry[$name]);
        } else {
            $actor = new $this->registry[$name];
        }

        return $this->actors[$name] = $actor;
    }
}
