<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionStatement;
use Poem\Actor\Exceptions\NotFoundException;

class Actor 
{
    use Module,
        Actable;

    const PREPARE_ACTION_EVENT = 'actor_prepare_action';
    
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
     * Create a new actor instance.
     * 
     * @param Story $story
     */
    function __construct(Story $story) 
    {
        $this->story = $story;

        // Initialize behaviors if defined
        static::initializeBehaviors();
    }

    /**
     * Register an action by class or name and callback
     * 
     * @param string $actionClass
     * @param callable $initializer
     */
    function registerAction(string $actionClass, callable $initializer = null) 
    {
        if(class_exists($actionClass)) {
            $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
        } else {
            $this->actions[$actionClass] = compact('initializer');
        }
    }

    /**
     * Test if this actor has an action with a specific name
     * 
     * @return bool
     */
    function hasAction(string $name): bool 
    {
        return isset($this->actions[$name]);
    }

    /**
     * @TODO: Move to action statement (maybe?)
     */
    function executeAction(string $actionType, array $payload = []) 
    {
        $subject = static::getSubjectClass();
        extract($this->actions[$actionType]);

        if(isset($actionClass)) {
            $type = $actionClass::getType();

            /** @var Action $action */
            $action = new $actionClass;
            $action->setSubject($subject);
            $action->setPayload($payload);

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

    /**
     * Build and return a action statement
     * 
     * @param string $actionType
     * @param array $payload
     * @return ActionStatement
     */
    function prepareAction(string $actionType, array $payload = []) 
    {
        $this->dispatchEvent(self::PREPARE_ACTION_EVENT, [
            'actionType' => $actionType, 
            'payload' => $payload
        ]);

        $this->initialize($actionType, $payload);

        $subject = static::getSubjectClass();

        if(!$this->hasAction($actionType)) {
            throw new NotFoundException("Action " . $actionType . " is not registered on " . $subject::Type);
        }

        return new ActionStatement($this, $actionType, $payload);

    }

    /**
     * Initialize the action before execution
     * 
     * @param string $actionType
     * @param array $payload
     */
    function initialize(string $actionType, array $payload = []) 
    {
        // Override for initialization
    }
}