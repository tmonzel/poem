<?php

namespace Poem;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

/**
 * Simple wrapper class for assoc arrays
 */
class Bag implements IteratorAggregate, JsonSerializable 
{   
    /**
     * Source params
     * 
     * @var array
     */
    protected $_params;

    function __construct(array $params = [])
    {
        $this->_params = $params;
    }

    function __get($name) 
    {
        return $this->_params[$name];
    }

    function __set($name, $value): void
    {
        $this->set($name, $value);
    }

    function set(string $name, $value): void 
    {
        $this->_params[$name] = $value;
    }

    /**
     * Check existence of parameter
     * 
     * @param string $name
     * @return bool
     */
    function has(string $name): bool 
    {
        return isset($this->_params[$name]);
    }

    /**
     * Inverse of has()
     * 
     * @param string $name
     * @return bool
     */
    function missing(string $name): bool
    {
        return !$this->has($name);
    }

    /**
     * 
     */
    function present(string $name) 
    {
        return $this->has($name) && !empty($this->_params[$name]);
    }

    function toArray() 
    {
        return $this->_params;
    }

    function getIterator()
    {
        return new ArrayIterator($this->_params);
    }

    function jsonSerialize()
    {
        return $this->_params;
    }
}