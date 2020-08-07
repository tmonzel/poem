<?php

namespace Order;

class Model extends \Poem\Model 
{
    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
    const Schema = [
        'id' => 'pk',
        'state' => 'string'
    ];

    /**
     * Order has many items
     * 
     * @var array
     */
    const HasMany = [
        'items' => 'order_items'
    ];
}