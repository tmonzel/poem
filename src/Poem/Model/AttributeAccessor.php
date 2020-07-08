<?php

namespace Poem\Model;

trait AttributeAccessor {
    protected $attributes = [];

    function __get($name) {
        return $this->readAttribute($name);
    }

    function __set($name, $value) {
        $this->writeAttribute($name, $value);
    }

    function __isset($name) {
        return isset($this->attributes[$name]);
    }

    function readAttribute($name) {
        if(isset($this->relations[$name])) {
            return $this->relations[$name];
        }

        if($this->hasRelation($name)) {
            // Connect
            return $this->connectRelation($name);
        }

        return $this->attributes[$name];
    }

    function writeAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }

    function writeAttributes(array $attributes) {
        foreach($attributes as $name => $value) {
            $this->attributes[$name] = $value;
        }
    }
}