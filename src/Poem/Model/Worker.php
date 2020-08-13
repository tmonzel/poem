<?php

namespace Poem\Model;

use Poem\Model;

class Worker
{
    /**
     * Worker accessor key
     * 
     * @var string
     */
    const Accessor = 'model';
    
    /**
     * Registered collection classes by type
     * 
     * @var array
     */
    protected $registry = [];

    /**
     * Created collection instances
     * 
     * @var array
     */
    protected $collections = [];

    /**
     * Added initializer callables
     * 
     * @var array
     */
    protected $initializers = [];

    /**
     * Access collection instance via property
     * 
     * @param string $name
     * @return Collection
     */
    function __get(string $name) 
    {
        return $this->access($name);
    }

    /**
     * 
     * 
     * @param string $type
     * @param callable $initializer
     */
    function addInitializer(string $type, callable $initializer) 
    {
        if(!isset($this->initializers[$type])) {
            $this->initializers[$type] = [];
        }

        $this->initializers[$type][] = $initializer;
    }

    /**
     * Register a collection by type and class
     * 
     * @param mixed $type
     * @param string $collectionClass
     */
    function register(string $type, $collectionClass, array $options = []): void
    {
        $this->registry[$type] = compact('collectionClass', 'options');
    }

    /**
     * Access a collection instance from the given type
     * 
     * @param string $type
     * @return Collection
     */
    function access(string $type): Collection 
    {
        if(isset($this->collections[$type])) {
            return $this->collections[$type];
        }

        $collectionClass = Model::class;
        $options = [];
        
        if(isset($this->registry[$type])) {
            $collectionClass = $this->registry[$type]['collectionClass'];
            $options = $this->registry[$type]['options'] + compact('type');
        }

        if(is_callable($collectionClass)) {
            $collection = $collectionClass();
        } else {
            $collection = new $collectionClass($options);
        }
        

        if(isset($this->initializers[$type])) {
            foreach($this->initializers[$type] as $initializer) {
                $initializer($collection);
            }
        }

        return $this->collections[$type] = $collection;
    }
}
