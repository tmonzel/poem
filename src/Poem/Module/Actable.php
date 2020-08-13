<?php

namespace Poem\Module;

use Poem\Actor;

trait Actable 
{
    function buildActor(): Actor 
    {
        $actor = new Actor();

        if(method_exists($this, 'accessModel')) {
            $actor->setModel($this->accessModel());
        }

        if(method_exists($this, 'withActor')) {
            $this->withActor($actor);
        }

        return $actor;
    }
}
