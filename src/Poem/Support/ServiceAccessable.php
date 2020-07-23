<?php

namespace Poem\Support;

trait ServiceAccessable 
{
    /**
     * Service provider instance
     * 
     * @var ServiceProvider
     */
    protected $provider;

    /**
     * Access service via magic getter
     * 
     * @return mixed
     */
    function __get($name) 
    {
        return $this->provider->accessService($name);
    }

    /**
     * Set the service provider for this object
     * 
     * @param ServiceProvider $provider
     */
    function setProvider(ServiceProvider $provider) 
    {
        $this->provider = $provider;
    }
}
