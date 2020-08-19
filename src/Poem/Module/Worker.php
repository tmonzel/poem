<?php

namespace Poem\Module;

use Exception;
use Poem\Module;
use Poem\Model\Accessor as ModelAccessor;

class Worker 
{
    use ModelAccessor;

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
     * Register an actor by class.
     * 
     * @param string $actorClass
     */
    function register(string $moduleClass): void
    {
        $this->registry[$moduleClass::getName()] = $moduleClass;

        // Check if module is storable register model type
        if(method_exists($moduleClass, 'getType')) {
            static::Model()->register(
                $moduleClass::getType(), 
                $moduleClass::getModelBuilder()
            );
        }
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
