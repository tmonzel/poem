<?php

namespace Poem\Actor;

use Poem\Director;

trait Accessor 
{
    /**
     * Provides the actor worker
     * 
     * @static
     * @return Worker
     */
    static function Actors(): Worker {
        return Director::provide(Worker::class);
    }
}