<?php

namespace User {
    use Poem\Model\Validateable;

    class Model extends \Poem\Model {
        use Validateable;

        /**
         * Type definition
         * 
         * @var string
         */
        const Type = 'users';

        /**
         * Public attributes
         * 
         * @var array
         */
        const Attributes = [
            'name'
        ];

        /**
         * User schema
         * 
         * @var array
         */
        const Schema = [
            'name' => 'string',
            'password' => 'string',
            'role' => Role::class
        ];

        /**
         * User belongs to one role
         * 
         * @var array
         */
       /* const BelongsTo = [
            'role' => Role::class
        ];*/

        /**
         * User validations
         * 
         * @static
         * @return array
         */
        static function validations(): array
        {
            return [
                'name' => 'required',
                'password' => 'required'
            ];
        }
    }
}
