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
            'id',
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
         * Should mutate attributes before create or update
         * 
         * @TODO: Implement this functionality
         */
        function mutateAttributes(array $attributes) 
        {
            if(isset($attributes['password'])) {
                $attributes['password'] = password_hash($attributes['password'], PASSWORD_ARGON2I);
            }

            return $attributes;
        }

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
