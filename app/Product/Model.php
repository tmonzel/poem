<?php

namespace Product {

    use Poem\Model\Validateable;

    class Model extends \Poem\Model {
        use Validateable;

        static $type = 'products';

        static function validations(): array {
            return [
                'name' => ['required']
            ];
        }
    }
}