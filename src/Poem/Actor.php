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
        Module\Helpers,
        Model\Accessor;

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
     * Applied model instance
     * 
     * @var Model $model
     */
    protected $model;

    /**
     * Create a new actor instance.
     * 
     * @param Module $module
     */
    function __construct() 
    {
        // Collect actions from constant
        static::withDefinedConstant('Actions', function($actionClasses) {
            foreach($actionClasses as $actionClass) {
                $this->registerAction($actionClass);
            }
        });

        // Initialize user defined stuff
        $this->initialize();
    }

    /**
     * Access the namespace related model for this actor.
     * 
     * @return Model
     */
    function getModel(): Model 
    {
        return $this->model;
    }

    /**
     * Sets the given model
     * 
     * @param Model $model
     * @return void
     */
    function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Register an action by class or name and callback
     * 
     * @param string $actionClass
     * @param callable $initializer
     */
    function registerAction(string $actionClass, callable $initializer = null): void 
    {
        if(class_exists($actionClass)) {
            $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
        } else {
            $this->actions[$actionClass] = compact('initializer');
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
     * Test if this actor has an action with a specific name
     * 
     * @return bool
     */
    function hasAction(string $name): bool 
    {
        return isset($this->actions[$name]);
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
        
        extract($this->actions[$actionType]);

        if(isset($initializer)) {
            return $initializer($query->getPayload());
        }

        if(isset($actionClass)) {
            $type = $actionClass::getType();

            /** @var Action $action */
            $action = new $actionClass($this);
            $action->setPayload($query->getPayload());

            if(isset($initializer)) {
                $initializer($action);
            }

            $this->beforeAction($action);
    
            if(method_exists($this, $type)) {
                $this->{$type}($action);
            }

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
    function prepareAction(string $actionType, array $payload = []) 
    {
        $query = new ActionQuery($this, $actionType, $payload);
        
        $this->dispatchEvent(self::PREPARE_ACTION_EVENT, $query);

        if(!$this->hasAction($actionType)) {
            throw new NotFoundException("Action `$actionType` is not registered");
        }

        return $query;
    }


    function canActivate(callable $test)
    {
        $this->addEventListener(self::PREPARE_ACTION_EVENT, function(ActionQuery $query) use($test) {
            $type = $query->getType();

            if(call_user_func($test, $query) === false) {
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
