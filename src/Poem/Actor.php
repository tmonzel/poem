<?php

namespace Poem;

use Poem\Actor\Action;
use Poem\Actor\ActionQuery;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Actor\Worker;
use Poem\Model\Accessor as ModelAccessor;

class Actor 
{
    use Module,
        Mutable,
        ModelAccessor;

    /**
     * Prepare action event key
     * 
     * @var string
     */
    const PREPARE_ACTION_EVENT = 'actor_prepare_action';
    
    /**
     * Registered actions
     * 
     * @var array
     */
    protected $actions = [];

    /**
     * Create a new actor instance.
     * 
     * @param Module $module
     */
    function __construct() 
    {
        // Initialize behaviors if defined
        static::initializeBehaviors();

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
     * Initializes this actor on configuration layer
     * Also called on Actor\Worker::register()
     * 
     * @param Worker $worker
     */
    static function register(Worker $worker): void
    {
        static::withNamespaceClass('Model', function($modelClass) {
            static::Model()->register(static::getType(), $modelClass, [
                'name' => static::getName()
            ]);
        });
    }

    /**
     * Returns the static actor type
     * 
     * @static
     * @return string
     */
    static function getType(): string 
    {
        return (static::class)::Type;
    }

    /**
     * Returns the actor name (singular of type)
     * 
     * @static
     * @return string
     */
    static function getName(): string 
    {
        return strtolower(substr(static::class, 0, strrpos(static::class, '\\')));
    }

    /**
     * Access the namespace related model for this actor.
     * 
     * @return Model
     */
    function accessModel(): Model 
    {
        return static::Model()->access(static::getType());
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
     * Test if this actor has an action with a specific name
     * 
     * @return bool
     */
    function hasAction(string $name): bool 
    {
        return isset($this->actions[$name]);
    }

    /**
     * Executes a given action.
     * 
     * @param string $actionType
     * @param array $payload
     * @return mixed
     */
    function executeAction(string $actionType, array $payload = []) 
    {
        extract($this->actions[$actionType]);

        if(isset($actionClass)) {
            $type = $actionClass::getType();

            /** @var Action $action */
            $action = new $actionClass($this);
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

        $calledClass = get_called_class();

        if(!$this->hasAction($actionType)) {
            throw new NotFoundException("Action " . $actionType . " is not registered on " . $calledClass::Type);
        }

        $query = new ActionQuery($this, $actionType, $payload);

        $this->beforeAction($query);

        return $query;

    }

    /**
     * Initialize the action before execution
     * 
     * @param ActionQuery $query 
     * @return void
     */
    function beforeAction(ActionQuery $query): void 
    {
        // Override for initialization
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