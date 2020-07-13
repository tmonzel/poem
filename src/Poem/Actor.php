<?php

namespace Poem;

use Poem\Actor\ActionDispatcher;
use Poem\Actor\ActionQuery;
use Poem\Actor\Exceptions\NotFoundException;

class Actor {
    static $type;

    protected $actions = [];

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

    function addAction(string $actionClass, callable $initializer = null) {
        $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
    }

    function hasAction($type): bool {
        return isset($this->actions[$type]);
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

    function invokeQuery(ActionQuery $query) {
        $this->initialize();
        $subject = static::getSubjectClass();

        if(!$this->hasAction($query->getType())) {
            throw new NotFoundException($query->getType() . " is not registered on " . $subject::Type);
        }

        extract($this->actions[$query->getType()]);

        $action = new $actionClass;
        $action->setSubject($subject);
        $action->setPayload($query->getPayload());

        return $action->dispatch();
    }

    function initialize() {
        // Override for initialization
    }

    /*function getDispatcher(): ActionDispatcher {
        $behaviors = $this->buildBehaviors();
        $subjectClass = static::getSubjectClass();
        $actions = new ActionDispatcher($subjectClass);
        
        foreach($behaviors as $behavior) {
            $behavior->prepareActions($actions);
        }
        
        $this->prepareActions($actions);

        return $actions;
    }*/
}