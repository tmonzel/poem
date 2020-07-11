<?php

namespace User {
    class Model extends \Poem\Model {
        const Type = 'users';
        const BelongsTo = [
            'role' => Role::class
        ];

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