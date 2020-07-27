<?php

namespace Poem;

use Exception;

class Director 
{
    /**
     * Assigned director instance
     * 
     * @static
     * @var Director
     */
    private static $assignedDirector;

    /**
     * Hired worker classes
     * 
     * @var array
     */
    protected $hiredWorkers = [];
    
    /**
     * Initialized worker instances
     * 
     * @var array
     */
    protected $workers = [];

    /**
     * Returns the assigned director instance
     * 
     * @static
     * @return Director
     */
    static function get(): Director
    {
        return static::$assignedDirector;
    }

    /**
     * Provide a worker by accessor
     * 
     * @static
     * @param string $accessor
     * @return mixed
     */
    static function provide(string $accessor)
    {
        return static::get()->accessWorker($accessor);
    }

    /**
     * Assign the director to use globally
     */
    function assign() 
    {
        static::$assignedDirector = $this;
    }

    /**
     * Walk each worker with a given interface
     * 
     * @param string $interface
     * @param callable $onEach
     */
    function eachWorkerWithInterface(string $interface, callable $onEach) 
    {
        foreach($this->getWorkerWithInterface($interface) as $worker) {
            $onEach($worker);
        }
    }

    /**
     * Returns all worker interfaces with a given interface
     * 
     * @param string $interface
     * @return array
     */
    function getWorkerWithInterface(string $interface): array
    {
        $services = array_filter($this->hiredWorkers, function($definition) use($interface) {
            $interfaces = class_implements($definition['workerClass']);
            return $interfaces ? isset($interfaces[$interface]) : false;
        });

        return array_map(function($accessor) { 
            return $this->accessWorker($accessor);
        }, array_keys($services));
    }

    /**
     * Hire a worker by class
     * 
     * @param string $workerClass
     * @param callable $initializer
     */
    function hire(string $workerClass, callable $initializer = null) 
    {
        $this->hiredWorkers[$workerClass::Accessor] = compact('workerClass', 'initializer');
    }
    
    /**
     * Access given worker by accessor
     * 
     * @param string $name
     * @return mixed
     */
    function accessWorker(string $name)
    {
        if(isset($this->workers[$name])) {
            return $this->workers[$name];
        }

        if(!isset($this->hiredWorkers[$name])) {
            throw new Exception("Worker with name $name not registered");
        }

        extract($this->hiredWorkers[$name]);

        if(!class_exists($workerClass)) {
            throw new Exception("Worker class " . $workerClass . " does not exist");
        }

        $worker = new $workerClass;

        if(isset($initializer)) {
            $initializer($worker);
        }

        return $this->workers[$name] = $worker;
    }
}
