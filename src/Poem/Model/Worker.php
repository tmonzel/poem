<?php

namespace Poem\Model;

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
     * Register a collection by type and class
     * 
     * @param string $type
     * @param string $collectionClass
     */
    function register(string $type, string $collectionClass, array $options = []): void
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

        $collectionClass = Collection::class;
        $options = [];
        
        if(isset($this->registry[$type])) {
            $collectionClass = $this->registry[$type]['collectionClass'];
            $options = $this->registry[$type]['options'] + compact('type');
        }

        return $this->collections[$type] = new $collectionClass($options);
    }
}
