<?php

namespace Poem\Module;

use Exception;
use Poem\Module;

class Worker 
{
    /**
     * Worker accessor key
     * 
     * @var string
     */
    const Accessor = 'module';

    /**
     * Registered module classes
     * 
     * @var array
     */
    protected $registry = [];

    /**
     * Created module instances
     * 
     * @var array
     */
    protected $modules = [];

    /**
     * Registers and boots a module.
     * 
     * @param string $moduleClass
     * @return void
     */
    function register(string $moduleClass): void
    {
        $this->registry[$moduleClass::getName()] = $moduleClass;

        // Booting
        $moduleClass::boot($this);
    }

    /**
     * Access a given module by name
     * 
     * @param string $name
     * @return Module
     */
    function access(string $name): Module 
    {
        if(isset($this->modules[$name])) {
            return $this->modules[$name];
        }

        if(!isset($this->registry[$name])) {
            throw new Exception("Module `$name` not registered");
        }

        return $this->modules[$name] = new $this->registry[$name];
    }
}
