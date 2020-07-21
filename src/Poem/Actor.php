<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionStatement;
use Poem\Actor\Exceptions\NotFoundException;

class Actor 
{
    use Module;

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
     * Parent story
     * 
     * @var Story
     */
    protected $story;

    /**
     * Auth helper
     * 
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new actor instance.
     * Builds all defined behaviors
     * 
     * @param Story $story,
     * @param Auth $auth
     */
    function __construct(Story $story) 
    {
        $this->story = $story;
        $this->auth = $story->getAuth();
        $this->behaviors = $this->buildBehaviors();
    }

    function registerAction(string $actionClass, callable $initializer = null) 
    {
        if(class_exists($actionClass)) {
            $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
        } else {
            $this->actions[$actionClass] = compact('initializer');
        }
    }

    function getAuth(): Auth {
        return $this->auth;
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

    function executeAction(string $actionType, array $payload = []) {
        $subject = static::getSubjectClass();
        extract($this->actions[$actionType]);

        if(isset($actionClass)) {
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

            return $action->execute();
        }

        if(isset($initializer)) {
            return $initializer($payload);
        }
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

        // $actionClass = $this->actions[$actionType]['actionClass'];

        return new ActionStatement($this, $actionType, $payload);

    }

    function initialize(string $actionType, array $payload = []) {
        // Override for initialization
    }
}