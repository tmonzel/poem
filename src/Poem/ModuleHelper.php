<?php

namespace Poem;

trait ModuleHelper 
{
    static function getNamespace(): string 
    {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getNamespaceClass(string $class): ?string 
    {
        $fullyQualifiedClassName = static::getNamespace() . '\\' . $class;

        if(class_exists($fullyQualifiedClassName)) {
            return $fullyQualifiedClassName;
        }

        return null;
    }

    static function withNamespaceClass(string $class, callable $doThat) 
    {
       if($fullyQualifiedClassName = static::getNamespaceClass($class)) {
            $doThat($fullyQualifiedClassName);
       }
    }

    static function withDefinedConstant($constantName, callable $doThat) {
        $calledClass = get_called_class();

        if(defined($calledClass . '::' . $constantName)) {
            $doThat(constant($calledClass . '::' . $constantName));
        }
    }
}