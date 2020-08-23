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
    protected $registry = [];
    
    /**
     * Initialized worker instances
     * 
     * @var array
     */
    protected $workers = [];

    /**
     * Returns or creates the assigned director instance
     * 
     * @static
     * @return Director
     */
    static function access(): Director
    {
        if(isset(static::$assignedDirector)) {
            return static::$assignedDirector;
        }
        
        return static::$assignedDirector = new static();
    }

    /**
     * Access a worker with the given key.
     * 
     * @static
     * @param string $accessor
     * @return mixed
     */
    static function provide(string $accessor)
    {
        return static::access()->accessWorker($accessor);
    }

    /**
     * Walk each worker with a given interface.
     * 
     * @param string $interface
     * @param callable $onEach
     * @return void
     */
    function eachWorkerWithInterface(string $interface, callable $onEach): void 
    {
        foreach($this->getWorkerWithInterface($interface) as $worker) {
            $onEach($worker);
        }
    }

    /**
     * Returns all worker interfaces with a given interface.
     * 
     * @param string $interface
     * @return array
     */
    function getWorkerWithInterface(string $interface): array
    {
        $services = array_filter($this->registry, function($definition) use($interface) {
            $interfaces = class_implements($definition['workerClass']);
            return $interfaces ? isset($interfaces[$interface]) : false;
        });

        return array_map(function($accessor) { 
            return $this->accessWorker($accessor);
        }, array_keys($services));
    }

    /**
     * Hire a worker with the given class.
     * 
     * @param string $workerClass
     * @param callable $initializer
     * @return void
     */
    function add(string $workerClass, callable $initializer = null): void
    {
        $this->registry[$workerClass] = compact('workerClass', 'initializer');
    }

    /**
     * Creates a new story from this director.
     * 
     * @return Story
     */
    function newStory(): Story 
    {
        return new Story($this);
    }

    /**
     * Shorthand for accessWorker()
     * 
     * @param string $className
     * @return mixed
     */
    function get(string $className)
    {
        return $this->accessWorker($className);
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

        if(!isset($this->registry[$name])) {
            throw new Exception("Worker with name $name not registered");
        }

        extract($this->registry[$name]);

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
