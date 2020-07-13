<?php

namespace Poem;

use Poem\Actor\ActionDispatcher;

class Actor {
    static $type;

    static function getType(): string {
        $subjectClass = static::getSubjectClass();
        return class_exists($subjectClass) ? $subjectClass::Type : static::$type;
    }

    static function getNamespace(): string {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getSubjectClass(): string {
        return static::getNamespace() . '\\Model';
    }

    function prepareActions(ActionDispatcher $actions) {

    }

    function buildBehaviors(): array {
        $behaviors = [];
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Behaviors')) {
            return $behaviors;
        }

        foreach($calledClass::Behaviors as $k => $behaviorClass) {
            if(is_numeric($k)) {
                $behaviors[] = new $behaviorClass($this);
            } else {
                $behaviors[] = new $k($this, $behaviorClass);
            }
        }

        return $behaviors;
    }

    function getDispatcher(): ActionDispatcher {
        $behaviors = $this->buildBehaviors();
        $subjectClass = static::getSubjectClass();
        $actions = new ActionDispatcher($subjectClass);
        
        foreach($behaviors as $behavior) {
            $behavior->prepareActions($actions);
        }
        
        $this->prepareActions($actions);

        return $actions;
    }
}