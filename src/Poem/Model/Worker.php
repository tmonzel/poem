<?php

namespace Poem\Model;

use Poem\Data\Accessor as DataAccessor;

class Worker
{
    use DataAccessor;
    
    /**
     * Worker accessor key 
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
    function register(string $type, string $collectionClass): void
    {
        $this->registry[$type] = $collectionClass;
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
        
        if(isset($this->registry[$type])) {
            $collectionClass = $this->registry[$type];
        }

        $connection = static::Data()->resolveConnection('default');

        return $this->collections[$type] = new $collectionClass(
            $type,
            $connection->getCollectionAdapter($type)
        );
    }
}
