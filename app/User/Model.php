<?php

namespace User {

    use Poem\Auth\BehaveAsUser;
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
         * Behaviors for this model
         * 
         * @var array
         */
        const Behaviors = [
            BehaveAsUser::class
        ];

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
