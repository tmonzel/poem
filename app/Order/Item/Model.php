<?php

namespace Order\Item;

class Model extends \Poem\Model {
    const Type = 'order_items';

    const Schema = [
        'id' => 'pk',
        'order' => \Order\Model::class,
        'product' => \Product\Model::class,
        'market' => \Market\Model::class
    ];
}