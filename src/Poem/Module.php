<?php

namespace Poem;

use Poem\Module\Worker;

class Module
{
    use ModuleHelper;

    /**
     * Returns the module name
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
     * Initializes this actor on configuration layer
     * Also called on Module\Worker::register()
     * 
     * @param Worker $worker
     */
    static function register(Worker $worker): void
    {
        /*$modelClass = static::$modelClass ?? static::getNamespaceClass('Model');
        $documentClass = static::getNamespaceClass('Document');
        
        static::Model()->register(static::getType(), $modelClass ?? Model::class, [
            'name' => static::getName(),
            'relationships' => static::getRelationships(),
            'documentClass' => $documentClass
        ]);

        static::Model()->addInitializer(
            static::getType(), 
            get_called_class() . '::withModel'
        );*/
    }
}
