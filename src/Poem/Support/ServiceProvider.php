<?php

namespace Poem\Support;

use Exception;

class ServiceProvider {
    private $registeredServices = [];
    private $services = [];

    function registerService(string $identifier, string $class) {
        $this->registeredServices[$identifier] = $class;
    }
    
    function accessService($name) {
        if(isset($this->services[$name])) {
            return $this->services[$name];
        }

        if(!isset($this->registeredServices[$name])) {
            throw new Exception("Service with name $name not registered");
        }

        if(!class_exists($this->registeredServices[$name])) {
            throw new Exception("Service class " . $this->registeredServices[$name] . " does not exist");
        }

        return $this->services[$name] = new $this->registeredServices[$name];
    }
}