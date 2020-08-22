<?php

namespace Poem;

use Poem\Module\Worker;

/**
 * Modules serve as singleton factories for actors
 * and models related to its namespace.
 */
class Module
{
    use Module\Helpers,
        Model\Accessor;

    /**
     * Creates a new module.
     */
    function __construct()
    {
        
    }

    /**
     * Returns the module name. By default a lowercased form of
     * the namespace`s basename is taken.
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
     * Boots the module on Worker::register()
     * 
     * @static
     * @param Worker $worker
     * @return void
     */
    static function boot(Worker $worker): void
    {
        $calledClass = get_called_class();

        if(isset($calledClass::$type)) {
            static::Model()->register(
                $calledClass::$type, 
                $calledClass::getModelBuilder()
            );
        }
    }
}
