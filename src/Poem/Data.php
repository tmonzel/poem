<?php

namespace Poem;

use Exception;
use Poem\Data\Client;
use Poem\Data\Connection;

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
     * Keeper of all resolved clients
     * 
     * @static
     * @var array
     */
    private static $clients = [];

    /**
     * Register
     * 
     * @static
     */
    static function registerConnection($connectionClass, array $config, $name = 'default')
    {
        self::$connections[$name] = compact('connectionClass', 'config');
    }

    /**
     * Returns an already resolved client or
     * creates and establishes a new one
     * 
     * @static
     * @param string $name
     * @return Client
     */
    static function resolveConnection($name): Connection 
    {   
        if(isset(static::$clients[$name])) {
            return static::$clients[$name];
        }

        if(!isset(static::$connections[$name])) {
            throw new Exception("Connection $name is not registered");
        }

        $connection = static::$connections[$name];

        /** @var Connection $client */
        $client = new $connection['connectionClass'];
        $client->connect($connection['config']);

        return static::$clients[$name] = $client;
    }
}