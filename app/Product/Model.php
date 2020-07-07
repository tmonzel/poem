<?php

namespace Product {

    use Poem\Model\Validateable;

    class Model extends \Poem\Model {
        use Validateable;

        const Type = 'products';

        static function validations(): array {
            return [
                'name' => ['required']
            ];
        }
    }
}