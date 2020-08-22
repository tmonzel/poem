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
    protected $models = [];

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
     * @param mixed $type
     * @param string $modelClass
     */
    function register(string $type, $modelClass, array $options = []): void
    {
        $this->registry[$type] = compact('modelClass', 'options');
    }

    /**
     * Access a collection instance from the given type
     * 
     * @param string $type
     * @return Model
     */
    function access(string $type): Model 
    {
        if(isset($this->models[$type])) {
            return $this->models[$type];
        }

        $modelClass = Model::class;
        $options = [];
        
        if(isset($this->registry[$type])) {
            $modelClass = $this->registry[$type]['modelClass'];
            $options = $this->registry[$type]['options'] + compact('type');
        } else {
            $options['type'] = $type;
        }

        if(is_callable($modelClass)) {
            $model = $modelClass();
        } else {
            $model = new $modelClass($options);
        }

        return $this->models[$type] = $model;
    }
}
