<?php

namespace Poem\Data;

class ClientManager {
    private $clients = [];

    function __construct(array $clients = []) {
        foreach($clients as $name => $client) {
            $this->addClient($client, $name);
        }
    }

    function getClient($name): Client {
        return $this->clients[$name];
    }

    function resolveClient($name = null) {
        if(!$name) {
            return $this->clients['default'];
        }

        return $this->clients[$name];
    }

    function addClient(Client $client, $name = 'default') {
        $this->clients[$name] = $client;
    }
}