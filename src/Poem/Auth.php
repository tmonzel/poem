<?php

namespace Poem;

use Poem\Auth\Authenticator;

class Auth {
    private static $authenticator;

    static function getAuthenticator() {
        if(isset(static::$authenticator)) {
            return static::$authenticator;
        }

        return static::$authenticator = new Authenticator();
    }
}