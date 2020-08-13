<?php

namespace Poem\Module;

use Poem\Director;

trait Accessor 
{
    /**
     * Provides the module worker
     * 
     * @static
     * @return Worker
     */
    static function Module(): Worker {
        return Director::provide(Worker::Accessor);
    }
}