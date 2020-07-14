<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionQuery;
use Poem\Actor\Exceptions\NotFoundException;

class Actor {
    static $type;

    protected $behaviors = [];
    protected $actions = [];

    function __construct() {
        $this->behaviors = $this->buildBehaviors();
    }

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

    protected function buildBehaviors(): array {
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

    function invokeQuery(ActionQuery $query) 
    {
        foreach($this->behaviors as $behavior) {
            $behavior->initialize($query);
        }

        $this->initialize($query);
        $subject = static::getSubjectClass();

        if(!$this->hasAction($query->getType())) {
            throw new NotFoundException($query->getType() . " is not registered on " . $subject::Type);
        }

        extract($this->actions[$query->getType()]);

        /** @var Action $action */
        $action = new $actionClass;
        $action->setSubject($subject);
        $action->setPayload($query->getPayload());
        $action->setHeaders($query->getHeaders());

        foreach($this->behaviors as $behavior) {
            $behavior->prepareAction($action);
        }

        if(isset($initializer)) {
            $initializer($action);
        }

        if(method_exists($this, $action->getType())) {
            $this->{$action->getType()}($action);
        }

        return $action->dispatch();
    }

    function initialize(ActionQuery $query) {
        // Override for initialization
    }
}