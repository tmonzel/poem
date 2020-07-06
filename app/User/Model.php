<?php

namespace User {
    class Model extends \Poem\Model {
        static $type = 'users';

        /**
         * User schema
         * 
         * @static
         * @var array
         */
        static $schema = [
            'name' => 'string',
            'password' => 'string',
            'role' => 'int'
        ];

        /**
         * Public attributes
         * 
         * @static
         * @var array
         */
        static $serializable = [
            'name',
            'role'
        ];
    }
}