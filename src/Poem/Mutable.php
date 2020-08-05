<?php

namespace Poem;

trait Mutable 
{
    protected static $behaviors = [];

    static function buildBehaviors() {
        $behaviors = [];
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Behaviors')) {
            return $behaviors;
        }

        foreach($calledClass::Behaviors as $k => $behaviorClass) {
            if(is_numeric($k)) {
                $behaviors[] = new $behaviorClass();
            } else {
                $behaviors[] = new $k($behaviorClass);
            }
        }

        return $behaviors;
    }

    static function initializeBehaviors() {
        $calledClass = get_called_class();

        if(!isset(static::$behaviors[$calledClass])) {
            static::$behaviors[$calledClass] = static::buildBehaviors();
        }
    }

    function dispatchEvent(string $type, array $payload) {
        $calledClass = get_called_class();

        if(!isset(static::$behaviors[$calledClass])) {
            return;
        }

        foreach(static::$behaviors[$calledClass] as $behavior) {
            $behavior->dispatchEvent($this, $type, $payload);
        }
    }
}