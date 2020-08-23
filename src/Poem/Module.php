<?php

namespace Poem;

use Poem\Model\Document;

/**
 * Modules serve as singleton factories for actors
 * and models related to its namespace.
 */
class Module
{
    use Module\Helpers;

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
     * Boots the module and registers defined 
     * actors and models
     * 
     * @static
     * @param Director $director
     * @return void
     */
    static function boot(Director $director): void
    {
        $moduleClass = get_called_class();

        $actors = $director->get(Actor\Worker::class);
        $actors->register(static::getName(), function() use($director, $moduleClass) {
            $actorClass = static::getNamespaceClass('Actor') ?? Actor::class;

            /** @var Actor $actor */
            $actor = new $actorClass($director);
            
            if(isset($moduleClass::$type)) {
                $actor->useModelType($moduleClass::$type);
            }

            if(method_exists($moduleClass, 'prepareActor')) {
                $moduleClass::prepareActor($actor);
            }

            return $actor;
        });
        
        
        // Register model if type set
        if(isset($moduleClass::$type)) {
            $modelClass = static::getNamespaceClass('Model');

            if(isset($modelClass)) {
                $models = $director->get(Model\Worker::class);
                $models->register($moduleClass::$type, function() use($modelClass, $moduleClass) {

                    $documentClass = static::getNamespaceClass('Document') ?? Document::class;

                    $model = new $modelClass([
                        'type' => $moduleClass::$type,
                        'name' => static::getName(),
                        'documentClass' => $documentClass
                    ]);

                    if(method_exists($moduleClass, 'prepareModel')) {
                        $moduleClass::prepareModel($model);
                    }

                    return $model;
                });
            }
        }
    }
}
