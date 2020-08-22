<?php

namespace Poem\Module;

use Poem\Actor;

trait Actable 
{
    private $_actorInstance;

    function accessActor(): Actor
    {
        if(isset($this->_actorInstance)) {
            return $this->_actorInstance;
        }

        $actor = $this->buildActor();

        if(method_exists($this, 'accessModel')) {
            $actor->setModel($this->accessModel());
        }

        if(method_exists($this, 'withActor')) {
            $this->withActor($actor);
        }

        return $this->_actorInstance = $actor;
    }

    function buildActor(): Actor 
    {
        $calledClass = get_called_class();
        $actorClass = isset($calledClass::$actorClass) 
            ? $calledClass::$actorClass 
            : static::getNamespaceClass('Actor') ?? Actor::class;

        return new $actorClass;
    }
}
