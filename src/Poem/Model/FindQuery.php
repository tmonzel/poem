<?php

namespace Poem\Model;

use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Fluent document find query interface
 */
class FindQuery implements IteratorAggregate, JsonSerializable 
{
    /**
     * Applied iterator
     * 
     * @var Traversable
     */
    protected $iterator;

    /**
     * Source collection instance
     * 
     * @var Collection
     */
    protected $collection;

    /**
     * Collected query conditions
     * 
     * @var array
     */
    protected $conditions = [
        'filter' => [],
        'include' => null,
        'limit' => null,
        'sort' => null,
        'format' => null
    ];

    /**
     * Creates a new find query.
     * 
     * @param Collection $collection
     */
    function __construct(Collection $collection) 
    {
        $this->collection = $collection;
    }

    /**
     * Not implemented include condition.
     * 
     * @return $this
     */
    function include($include) 
    {
        $this->conditions['include'] = $include;
        return $this;
    }

    /**
     * Add format condition.
     * 
     * @param array $format
     * @return $this
     */
    function format(array $format) 
    {
        $this->conditions['format'] = $format;
        return $this;
    }

    /**
     * Adds a filter condition.
     * 
     * @param array $filter
     * @return $this
     */
    function filter(array $filter) 
    {
        $this->conditions['filter'] = $filter;
        return $this;
    }

    /**
     * Adds a limit condition.
     * 
     * @param int $limit
     * @return $this
     */
    function limit(int $limit) 
    {
        $this->conditions['limit'] = $limit;
        return $this;
    }


    /**
     * Not implemented sort condition.
     * 
     * @return $this
     */
    function sort($options) 
    {
        return $this;
    }

    /**
     * Executes and returns the first document of this query.
     * 
     * @return Document
     */
    function first(): ?Document
    {
        foreach($this as $document) {
            return $document;
        }
    }

    /**
     * Executes this query with the applied executor
     * and returns a traversable and serializable object.
     * 
     * @return Traversable
     */
    function execute(): Traversable 
    {
        $this->iterator = $this->collection->findWith($this->conditions); 
        return $this->iterator;
    }


    /**
     * Returns a traversable object.
     * 
     * @return Traversable
     */
    function getIterator(): Traversable
    {
        if(!$this->iterator) {
            $this->iterator = $this->execute();
        }

        return $this->iterator;
    }

    /**
     * Serializes to json.
     * 
     * @return Traversable
     */
    function jsonSerialize()
    {
        return $this->execute();
    }
}