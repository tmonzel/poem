<?php

namespace Poem;

trait Module {
    
    /**
     * Optional type definition 
     * Otherwise subject::Type is used
     * 
     * @var string
     */
    static $type;

    static function getType(): string 
    {
        $subjectClass = static::getSubjectClass();
        return class_exists($subjectClass) ? $subjectClass::Type : static::$type;
    }

    static function getNamespace(): string 
    {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getSubjectClass(): string 
    {
        return static::getNamespace() . '\\Model';
    }
}