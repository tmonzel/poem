<?php

namespace Poem\Data;

use Poem\Director;

trait Accessor {

    /**
     * Provide the data worker
     * 
     * @static
     * @return Worker
     */
    static function Data(): Worker {
        return Director::provide(Worker::Accessor);
    }
}