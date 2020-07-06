<?php

namespace Poem\Actor;

trait Storable {
    abstract static function schema(): array;

    static function getRepository() {
        
    }
}