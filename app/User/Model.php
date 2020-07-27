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
            'password' => 'string'
        ];

        /**
         * Mutate attributes before create or update
         * 
         * @static
         * @param array $attributes
         * @return array
         */
        protected static function mutateAttributes(array $attributes): array
        {
            if(isset($attributes['password'])) {
                $attributes['password'] = password_hash($attributes['password'], PASSWORD_ARGON2I);
            }

            return $attributes;
        }

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
