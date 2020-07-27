<?php

namespace Poem;

use Exception;

class Director {
    private static $assignedDirector;

    private $registeredServices = [];
    private $services = [];

    static function get() {
        return static::$assignedDirector;
    }

    static function provide($accessor) {
        return static::get()->accessWorker($accessor);
    }

    function assign() {
        static::$assignedDirector = $this;
    }

    function eachWorkerWithInterface($interface, callable $callback) {
        foreach($this->getWorkerWithInterface($interface) as $worker) {
            $callback($worker);
        }
    }

    function getWorkerWithInterface(string $interface) {
        $services = array_filter($this->registeredServices, function($definition) use($interface) {
            $interfaces = class_implements($definition['workerClass']);
            return $interfaces ? isset($interfaces[$interface]) : false;
        });

        return array_map(function($accessor) { 
            return $this->accessWorker($accessor);
        }, array_keys($services));
    }

    function hire(string $workerClass, callable $initializer = null) 
    {
        $this->registeredServices[$workerClass::Accessor] = compact('workerClass', 'initializer');
    }
    
    function accessWorker($name)
    {
        if(isset($this->services[$name])) {
            return $this->services[$name];
        }

        if(!isset($this->registeredServices[$name])) {
            throw new Exception("Worker with name $name not registered");
        }

        extract($this->registeredServices[$name]);

        if(!class_exists($workerClass)) {
            throw new Exception("Worker class " . $workerClass . " does not exist");
        }

        $worker = new $workerClass;

        if(isset($initializer)) {
            $initializer($worker);
        }

        return $this->services[$name] = $worker;
    }
}