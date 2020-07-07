<?php

namespace Retailer {

    class Model extends \Poem\Model {
        const Type = 'retailers';
        const HasMany = [
            'markets' => Market\Model::class
        ];
    }
}