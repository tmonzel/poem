<?php

namespace Poem\Module;

use Closure;
use Exception;
use Poem\Model;
use Poem\Model\Accessor as ModelAccessor;
use Poem\Model\Document;

trait Storable 
{
    use ModelAccessor;

    static function accessModel()
    {
        return static::Model()->access(static::getType());
    }

    static function getModelBuilder(): Closure
    {
        return function() {
            return static::buildModel();
        };
    }
    
    static function buildModel(): Model
    {
        $modelClass = static::getModelClass();

        $model = new $modelClass([
            'type' => static::getType(),
            'name' => static::getName(),
            'documentClass' => static::getDocumentClass()
        ]);

        if(method_exists(get_called_class(), 'withModel')) {
            static::withModel($model);
        }

        return $model;
    }

    static function migrate(): void {
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Schema')) {
            throw new Exception('No schema defined for ' . $calledClass);
        }

        static::accessModel()->accessAdapter()->migrate($calledClass::Schema);
    }

    static function getModelClass(): string
    {
        $calledClass = get_called_class();
        $modelClass = static::getNamespaceClass('Model') ?? Model::class;

        return isset($calledClass::$modelClass) ? $calledClass::$modelClass : $modelClass;
    }

    static function getDocumentClass(): string
    {
        $calledClass = get_called_class();
        $documentClass = static::getNamespaceClass('Document') ?? Document::class;

        return isset($calledClass::$documentClass) ? $calledClass::$documentClass : $documentClass;
    }

    abstract static function getType(): string;
}