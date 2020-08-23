<?php

namespace Poem\Model;

use Poem\Director;

trait Accessor 
{
    /**
     * Provides the model worker
     * 
     * @static
     * @return Worker
     */
    static function Model(): Worker {
        return Director::provide(Worker::class);
    }
}