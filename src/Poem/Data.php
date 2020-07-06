<?php

namespace Poem;

use Poem\Data\ClientManager;

class Data {
    static $clientManager;

    static function clients(): ClientManager {
        if(static::$clientManager) {
            return static::$clientManager;
        }

        return static::$clientManager = new ClientManager();
    }

    static function schema() {
        // return schema creator
    }
}