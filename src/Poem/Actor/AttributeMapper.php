<?php

namespace Poem\Actor;

trait AttributeMapper 
{
    /**
     * Holds the mapper callables by attribute
     * 
     * @var array
     */
    protected $mappersByAttribute = [];

    /**
     * Maps a given attribute
     * 
     * @param string $name
     * @param callable $mapper
     * @return void
     */
    function mapAttribute(string $name, callable $mapper): void 
    {
        $this->mappersByAttribute[$name] = $mapper;
    }

    /**
     * Apply all mappers on the given attributes
     * 
     * @param array $attributes
     * @return array
     */
    function map(array $attributes): array 
    {
        $result = [];

        foreach($attributes as $name => $value) {
            if(isset($this->mappersByAttribute[$name]) and is_callable($this->mappersByAttribute[$name])) {
                $result[$name] = call_user_func($this->mappersByAttribute[$name], $value);
                continue;
            }

            $result[$name] = $value;
        }

        return $result;
    }
}
