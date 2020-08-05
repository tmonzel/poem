<?php

namespace Order;

class Collection extends \Poem\Model\Collection 
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