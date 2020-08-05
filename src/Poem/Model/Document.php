<?php

namespace Poem\Model;

use JsonSerializable;

class Document implements JsonSerializable 
{
    /**
     * Document attributes
     * Represents all fields of a table row
     * 
     * @var array
     */
    protected $_attributes = [];

    /**
     * Document type
     * Always plural e.g. users, products...
     * 
     * @var string
     */
    protected $_type;

    /**
     * Hide this attributes from serialization
     * 
     * @var array
     */
    protected $_hiddenAttributes = [];

    /**
     * Holds all attribute names that were modified or added
     * after the initial creation
     * 
     * @var array
     */
    protected $_dirtyAttributes = [];

    /**
     * Holds all validation errors by attribute name
     * 
     * @var array
     */
    protected $_errors = [];

    /**
     * Format structure
     * 
     * @var array
     */
    protected $_format;

    /**
     * Create a new document.
     * 
     * @param string $type
     * @param array $attributes
     */
    function __construct(string $type, array $attributes = [])
    {
        $this->_type = $type;
        $this->_attributes = $attributes;

        if(!$this->exists()) {
            // If there is no id attribute mark all attributes as dirty
            foreach(array_keys($this->_attributes) as $attributeName) {
                $this->_dirtyAttributes[$attributeName] = true;
            }
        }

        $calledClass = get_called_class();

        if(defined($calledClass . '::Hide')) {
            $this->_hiddenAttributes = array_merge($this->_hiddenAttributes, $calledClass::Hide);
        }
    }

    /**
     * Read attribute via property
     * 
     * @param string $name
     * @return mixed
     */
    function __get($name) 
    {
        return $this->readAttribute($name);
    }

    /**
     * Write attribute via property
     * 
     * @param string $name
     * @param mixed $value
     */
    function __set($name, $value) 
    {
        $this->writeAttribute($name, $value);
    }

    /**
     * Check existence of attribute via property
     * 
     * @param string $name
     * @return bool
     */
    function __isset($name) 
    {
        return $this->has($name);
    }

    /**
     * Read a given attribute value
     * 
     * @param string $name
     * @return mixed
     */
    function readAttribute(string $name) 
    {
        if(isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
    }

    /**
     * Write a given attribute value
     * 
     * @param string $name
     * @param mixed $value
     */
    function writeAttribute(string $name, $value): void
    {
        $this->_dirtyAttributes[$name] = true;
        $this->_attributes[$name] = $value;
    }

    /**
     * Check existence of a given attribute
     * 
     * @param string $name
     * @return bool
     */
    function has(string $name): bool
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * Check if the given attribute name were modified
     * 
     * @param string $name
     * @return bool
     */
    function isDirty(string $name): bool
    {
        return isset($this->_dirtyAttributes[$name]);
    }

    /**
     * Fill attribute values
     * Uses Document::writeAttribute() on every iteration
     * 
     * @param array $attributes
     * @return void
     */
    function fill(array $attributes): void 
    {
        foreach($attributes as $name => $value) {
            $this->writeAttribute($name, $value);
        }
    }

    /**
     * Set serialization format.
     * 
     * @param array|null $format
     * @return void
     */
    function setFormat(?array $format): void 
    {
        $this->_format = $format;
    }

    /**
     * Sets validation errors for this document.
     * 
     * @param array $errors
     * @return void
     */
    function setErrors(array $errors): void 
    {
        $this->_errors = $errors;
    }

    /**
     * Check for validation errors
     * 
     * @return bool
     */
    function hasErrors(): bool 
    {
        foreach($this->_errors as $error) {
            return true;
        }

        return false;
    }

    /**
     * Returns all validation errors.
     * 
     * @return bool
     */
    function getErrors(): array 
    {
        return $this->_errors;
    }

    /**
     * Modest attempt to check if this document is new or not :-)
     * 
     * @return bool
     */
    function exists(): bool 
    {
        return $this->has('id');
    }

    /**
     * Interface implementation for json_encode
     * 
     * @return array
     */
    function jsonSerialize(): array
    {
        return $this->translate();
    }

    /**
     * Translate this document to a valid json encodable array
     * 
     * @return array
     */
    function translate(): array 
    {
        if(isset($this->_format)) {
            return $this->translateWithFormat($this->_format);
        }
        
        $type = $this->_type;
        $id = $this->id;
        $attributes = $this->collectVisibleAttributes();

        if(isset($attributes['id'])) {
            unset($attributes['id']);
        }

        return compact('type', 'id', 'attributes');
    }

    /**
     * Translate with custom format
     * 
     * @param array $format
     * @return array
     */
    function translateWithFormat(array $format): array
    {
        $attributes = [];
        $visibleAttributes = $this->collectVisibleAttributes();

        foreach($format as $n) {
            if(isset($visibleAttributes[$n])) {
                $attributes[$n] = $visibleAttributes[$n];
            }
        }

        return $attributes;
    }

    /**
     * Collects the not-hidden attributes
     * 
     * @return array
     */
    function collectVisibleAttributes(): array
    {
        return array_filter($this->_attributes, function($key) {
            return array_search($key, $this->_hiddenAttributes) === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return an array with the given attribute names
     * 
     * @param array $attributeNames
     * @return array
     */
    function extract(array $attributeNames): array
    {
        $result = [];

        foreach($attributeNames as $name) {
            $result[$name] = $this->readAttribute($name);
        }

        return $result;
    }

    /**
     * Return all attributes
     * 
     * @return array
     */
    function toArray(): array 
    {
        return $this->_attributes;
    }
}
