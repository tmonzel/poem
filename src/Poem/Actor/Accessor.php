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
    static function Actor(): Worker {
        return Director::provide(Worker::Accessor);
    }
}