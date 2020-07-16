<?php

namespace Poem;

use Exception;
use Poem\Data\Client;

class Data 
{
    /**
     * Registered connections
     * 
     * @static
     * @var array
     */
    private static $connections = [];

    /**
     * Resolved clients
     * 
     * @static
     * @var array
     */
    private static $clients = [];

    static function registerConnection($clientClass, array $config, $name = 'default') {
        self::$connections[$name] = compact('clientClass', 'config');
    }

    static function resolveConnection($name): Client 
    {   
        if(static::$clients[$name]) {
            return static::$clients[$name];
        }

        if(!isset(static::$connections[$name])) {
            throw new Exception("Connection $name is not registered");
        }

        $connection = static::$connections[$name];
        $client = new $connection['clientClass'];
        $client->establishConnection($connection['config']);

        return static::$clients[$name] = $client;
    }
}