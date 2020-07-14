<?php

namespace Poem;

use Poem\Auth\Authorizer;
use Poem\Auth\JwtService;

class Auth {

    /**
     * @var Authorizer
     */
    private static $authorizer;

    /**
     * 
     */
    private static $tokenizer;

    static function getTokenizer() {
        if(isset(self::$tokenizer)) {
            return self::$tokenizer;
        }

        return self::$tokenizer = new JwtService();
    }

    static function getAuthorizer() {
        if(isset(self::$authorizer)) {
            return self::$authorizer;
        }

        return self::$authorizer = new Authorizer();
    }



    static function authorize(string $token) {
        return static::$authorizer->authorize($token);
    }

    static function setAuthorizer(Authorizer $authorizer) {
        self::$authorizer = $authorizer;
    }
}