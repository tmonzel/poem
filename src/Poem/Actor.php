<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionQuery;
use Poem\Actor\ActionStatement;
use Poem\Actor\Exceptions\NotFoundException;

class Actor 
{
    static $type;

    /**
     * Initialized behaviors
     * 
     * @var array
     */
    protected $behaviors = [];

    /**
     * Registered actions
     * 
     * @var array
     */
    protected $actions = [];

    /**
     * @var Story
     */
    protected $story;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new actor instance.
     * Builds all defined behaviors
     * 
     */
    function __construct(Story $story) 
    {
        $this->story = $story;
        $this->behaviors = $this->buildBehaviors();
    }

    static function getType(): string 
    {
        $subjectClass = static::getSubjectClass();
        return class_exists($subjectClass) ? $subjectClass::Type : static::$type;
    }

    static function getNamespace(): string 
    {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getSubjectClass(): string 
    {
        return static::getNamespace() . '\\Model';
    }

    function setAuth(Auth $auth) 
    {
        $this->auth = $auth;
    }

    function registerAction(string $actionClass, callable $initializer = null) 
    {
        $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
    }

    function hasAction($type): bool 
    {
        return isset($this->actions[$type]);
    }

    protected function buildBehaviors(): array 
    {
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

    function isActionAllowed($type, $payload) {

    }

    function dispatchAction(string $actionClass, array $payload = []) {
        $subject = static::getSubjectClass();
        $type = $actionClass::getType();

        /** @var Action $action */
        $action = new $actionClass;
        $action->setSubject($subject);
        $action->setPayload($payload);

        foreach($this->behaviors as $behavior) {
            $behavior->prepareAction($action);
        }

        if(isset($initializer)) {
            $initializer($action);
        }

        if(method_exists($this, $type)) {
            $this->{$type}($action);
        }

        return $action->dispatch();
    }

    function prepareAction(string $actionType, array $payload = []) {
        foreach($this->behaviors as $behavior) {
            $behavior->initialize($this, $actionType, $payload);
        }

        $this->initialize($actionType, $payload);

        $subject = static::getSubjectClass();

        if(!$this->hasAction($actionType)) {
            throw new NotFoundException("Action " . $actionType . " is not registered on " . $subject::Type);
        }

        if(!$this->isActionAllowed($actionType, $payload)) {
            // throw unauthorized error
        }

        $actionClass = $this->actions[$actionType]['actionClass'];

        return new ActionStatement($this, $actionClass, $payload);

    }

    function initialize(string $actionType, array $payload = []) {
        // Override for initialization
    }
}