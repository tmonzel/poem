<?php

namespace Poem\Actor;

use Exception;
use Poem\Actor;

class Worker 
{
    /**
     * Worker accessor key
     * 
     * @var string
     */
    const Accessor = 'actor';

    /**
     * Registered actor classes
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
     * Register an actor by class.
     * 
     * @param string $actorClass
     */
    function register(string $actorClass): void
    {
        $this->registry[$actorClass::getType()] = $actorClass;
        $actorClass::register($this);
    }

    /**
     * Access a given actor by type
     * 
     * @param string $type
     * @return Actor
     */
    function access(string $type): Actor 
    {
        if(isset($this->actors[$type])) {
            return $this->actors[$type];
        }

        if(!isset($this->registry[$type])) {
            throw new Exception("Actor `$type` not registered");
        }

        return new $this->registry[$type];
    }
}
