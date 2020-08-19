<?php

namespace Poem\Module;

use Poem\Actor;

trait Actable 
{
    function buildActor(): Actor 
    {
        $actorClass = static::getActorClass();
        $actor = new $actorClass;

        if(method_exists($this, 'accessModel')) {
            $actor->setModel($this->accessModel());
        }

        if(method_exists($this, 'withActor')) {
            $this->withActor($actor);
        }

        return $actor;
    }

    static function getActorClass(): string
    {
        $calledClass = get_called_class();
        $actorClass = static::getNamespaceClass('Actor') ?? Actor::class;

        return isset($calledClass::$actorClass) ? $calledClass::$actorClass : $actorClass;
    }
}
