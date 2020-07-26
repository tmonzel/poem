<?php

namespace Poem\Auth;

use Poem\Director;

trait Accessor {
    
    /**
     * Provide the auth worker
     * 
     * @static
     * @return Worker
     */
    static function Auth(): Worker {
        return Director::provide(Worker::Accessor);
    }
}