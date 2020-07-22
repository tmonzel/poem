<?php

namespace Order;

class Item extends \Poem\Model {
    const Type = 'order_items';

    const Schema = [
        'id' => 'pk',
        'order' => Model::class,
        'product' => \Product\Model::class,
        'market' => \Market\Model::class
    ];
}