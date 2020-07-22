<?php

namespace Product {
    use Poem\Model\Validateable;

    class Model extends \Poem\Model {
        use Validateable;

        /**
         * Product model type
         * 
         * @var string
         */
        const Type = 'products';

        /**
         * Product schema
         * 
         * @var array
         */
        const Schema = [
            'id' => 'pk',
            'name' => 'string',
        ];

        /**
         * Product validations forced by Validateable
         * 
         * @static
         * @return array
         */
        static function validations(): array {
            return [
                'name' => ['required']
            ];
        }
    }
}