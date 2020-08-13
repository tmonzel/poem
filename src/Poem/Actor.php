<?php

namespace Poem;

use Exception;
use Poem\Actor\Action;
use Poem\Actor\ActionQuery;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Actor\Worker;
use Poem\Model\Accessor as ModelAccessor;

/**
 * Concrete actors must/can define the following
 * - Type (required)
 * - Schema (optional)
 * - Relationships (optional)
 * - Behaviors (optional)
 * - Actions (optional)
 */
abstract class Actor 
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
     * Custom model class used by this actor
     * 
     * @var string
     */
    static $modelClass;
    
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
        $modelClass = static::$modelClass ?? static::getNamespaceClass('Model');
        $documentClass = static::getNamespaceClass('Document');
        
        static::Model()->register(static::getType(), $modelClass ?? Model::class, [
            'name' => static::getName(),
            'relationships' => static::getRelationships(),
            'documentClass' => $documentClass
        ]);

        static::Model()->addInitializer(
            static::getType(), 
            get_called_class() . '::withModel'
        );
    }

    static function withModel(Model $model)
    {

    }

    /**
     * Returns all relationships used by the
     * related model.
     * 
     * @static
     * @return array
     */
    static function getRelationships(): array 
    {
        $calledClass = get_called_class();

        if(defined($calledClass . '::Relationships')) {
            return (static::class)::Relationships;
        }

        return [];
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
        $namespace = static::getNamespace();
        return strtolower(substr($namespace, strrpos($namespace, '\\') + 1));
    }

    /**
     * Access the namespace related model for this actor.
     * 
     * @return Model
     */
    static function accessModel(): Model 
    {
        return static::Model()->access(static::getType());
    }

    static function migrate(): void {
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Schema')) {
            throw new Exception('No schema defined for ' . $calledClass);
        }
        
        static::accessModel()->accessAdapter()->migrate($calledClass::Schema);
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
        $this->dispatchEvent(self::PREPARE_ACTION_EVENT, [
            'actionType' => $actionType, 
            'payload' => $payload
        ]);

        if(!$this->hasAction($actionType)) {
            throw new NotFoundException("Action " . $actionType . " is not registered on " . static::getType());
        }

        return new ActionQuery($this, $actionType, $payload);
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
