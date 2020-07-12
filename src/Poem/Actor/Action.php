<?php

namespace Poem\Actor;

abstract class Action {
    static $method;
    static $route;
    static $type;
    
    protected $subject;
    protected $payload = [];

    static function getType(): string {
        return static::$type;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setPayload(array $payload) {
        $this->payload = $payload;
    }

    function dispatch() {
        return $this->prepareData();
    }

    function prepareData() {
        
    }
}