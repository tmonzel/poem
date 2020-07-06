<?php

namespace Poem\Actor;

trait AttributeMapper {
    protected $mappersByAttribute = [];

    function mapAttribute($name, callable $mapper) {
        $this->mappersByAttribute[$name] = $mapper;
    }

    function map(array $attributes) {
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