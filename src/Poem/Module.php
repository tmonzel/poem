<?php

namespace Poem;

class Module
{
    use Module\Helpers;

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
}
