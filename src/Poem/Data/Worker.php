<?php

namespace Poem\Data;

use Exception;

class Worker 
{
    /**
     * Registered connections
     * 
     * @static
     * @var array
     */
    private $connections = [];

    /**
     * Keeper of all resolved clients
     * 
     * @static
     * @var array
     */
    private $clients = [];

    /**
     * Register connection
     * 
     */
    function registerConnection(string $connectionClass, array $config, $name = 'default')
    {
        $this->connections[$name] = compact('connectionClass', 'config');
    }

    /**
     * Returns an already resolved client or
     * creates and establishes a new one
     * 
     * @param string $name
     * @return Client
     */
    function resolveConnection($name): Connection 
    {   
        if(isset($this->clients[$name])) {
            return $this->clients[$name];
        }

        if(!isset($this->connections[$name])) {
            throw new Exception("Connection $name is not registered");
        }

        $connection = $this->connections[$name];

        /** @var Connection $client */
        $client = new $connection['connectionClass'];
        $client->connect($connection['config']);

        return $this->clients[$name] = $client;
    }
}