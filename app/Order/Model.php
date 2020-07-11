<?php

namespace Order {
    class Model extends \Poem\Model {
        const Type = 'orders';
        const Schema = [
            'id' => 'pk',
            'state' => 'string'
        ];
    }
}