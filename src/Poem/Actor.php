<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionQuery;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\UpdateAction;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth\Guardable;
use Poem\Model;
use Poem\Module;

/**
 * Concrete actors must/can define the following
 * - Type (required)
 * - Schema (optional)
 * - Relationships (optional)
 * - Behaviors (optional)
 * - Actions (optional)
 */
class Actor 
{
    use EventDispatcher,
        Guardable;

    /**
     * Prepare action event key
     * 
     * @var string
     */
    const PREPARE_ACTION_EVENT = 'actor.prepare_action';

    /**
     * Composed resource actions
     * 
     * @var array
     */
    const RESOURCE_ACTIONS = [
        FindAction::class,
        CreateAction::class,
        UpdateAction::class,
        DestroyAction::class
    ];
    
    /**
     * Registered actions
     * 
     * @var array
     */
    protected $actions = [];

    /**
     * Used model type
     * 
     * @var string
     */
    protected $modelType;

    /**
     * Applied director instance
     * 
     * @var Director
     */
    protected $director;

    /**
     * Create a new actor instance.
     * 
     * @param Module $module
     */
    function __construct(Director $director) 
    {
        $this->director = $director;

        // Initialize user defined stuff
        $this->initialize();
    }

    /**
     * Access the related model for this actor.
     * 
     * @return Model
     */
    function getModel(): Model 
    {
        return $this->director->get(Model\Worker::class)->access($this->modelType);
    }

    /**
     * Sets the given model type
     * 
     * @param string $type
     * @return void
     */
    function useModelType(string $type): void
    {
        $this->modelType = $type;
    }

    /**
     * Register an action by class or name and callback
     * 
     * @param string $actionClass
     * @param mixed $options
     */
    function registerAction(string $actionClass, $options = null): void 
    {
        if(is_array($options) && isset($options['alias'])) {
            $type = $options['alias'];
        } elseif(class_exists($actionClass)) {
            $type = $actionClass::getType();
        } else {
            $type = $actionClass;
        }

        $this->actions[$type] = compact('actionClass', 'options');
        
        if(class_exists($actionClass)) {
            $this->actions[$actionClass::getType()] = compact('actionClass', 'options');
        } else {
            $this->actions[$actionClass] = compact('options');
        }
    }

    /**
     * Bind one or many actions
     * Shorthand for registerAction()
     * 
     * @param mixed $actions
     * @return void
     */
    function bind($actions): void 
    {
        if(is_string($actions)) {
            $actions = [$actions];
        }

        foreach($actions as $actionClass) {
            $this->registerAction($actionClass);
        }
    }

    /**
     * Test if this actor has an action with a specific type
     * 
     * @param string $type
     * @return bool
     */
    function hasAction(string $type): bool 
    {
        return isset($this->actions[$type]);
    }

    /**
     * Test if an action can be triggered by this actor
     * 
     * @param string $actionType
     * @return bool
     */
    function canTrigger(string $actionType): bool
    {
        return $this->hasAction($actionType) || method_exists($this, $actionType . 'Action');
    }

    /**
     * Executes a given action query.
     * 
     * @param ActionQuery $query
     * @return mixed
     */
    function execute(ActionQuery $query) 
    {
        $actionType = $query->getType();
        $options = [];
        
        if(method_exists($this, $actionType . 'Action')) {
            return $this->{$actionType . 'Action'}($query->getPayload());
        }

        extract($this->actions[$actionType]);

        if(is_callable($options)) {
            return $options($query->getPayload());
        }

        if(isset($actionClass)) {
            $type = $actionClass::getType();

            /** @var Action $action */
            $action = new $actionClass($this);
            $action->setPayload($query->getPayload());

            if(isset($options) && $options['initializer']) {
                $initializer($action);
            }

            $this->beforeAction($action);

            return $this->afterAction(
                $action->execute()
            );
        }
    }

    /**
     * Build and return a action query
     * 
     * @param string $actionType
     * @param array $payload
     * @return ActionQuery
     */
    function prepareAction(string $actionType, array $payload = []): ActionQuery 
    {
        $query = new ActionQuery($this, $actionType, $payload);
        
        $this->dispatchEvent(self::PREPARE_ACTION_EVENT, $query);

        if(!$this->canTrigger($actionType)) {
            throw new NotFoundException("Action `$actionType` is not registered");
        }

        return $query;
    }


    function canActivate(callable $conditional): void
    {
        $this->addEventListener(self::PREPARE_ACTION_EVENT, function(ActionQuery $query) use($conditional) {
            $type = $query->getType();

            if(call_user_func($conditional, $query) === false) {
                throw new UnauthorizedException("Action `$type` not allowed");
            }
        });
    }

    /**
     * Do stuff before the given action executes
     * 
     * @param Action $action 
     * @return void
     */
    function beforeAction(Action $action): void 
    {
        // Override
    }

    /**
     * Modify the action result
     * 
     * @param mixed $result 
     * @return void
     */
    function afterAction($result) 
    {
        return $result;
    }

    /**
     * Called after construction
     * 
     * @return void
     */
    function initialize(): void
    {
        // Override for initialization
    }
}
