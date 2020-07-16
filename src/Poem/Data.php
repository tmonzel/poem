<?php

namespace Poem;

use Poem\Data\ClientManager;

class Data {
    static $resolverClass = ClientManager::class;

    private static $clientManager;

    /**
     * Returns the data clients container which holds all the 
     * connectable clients
     * 
     * @static
     * @return ClientManager
     */
    static function clients(): ClientManager 
    {
        if(static::$clientManager) {
            return static::$clientManager;
        }

        return static::$clientManager = new static::$resolverClass;
    }
}