<?php

namespace Market {
    class Model extends \Poem\Model {
        const Type = 'markets';
        const BelongsTo = [
            'retailer' => Retailer\Model::class
        ];
    }
}