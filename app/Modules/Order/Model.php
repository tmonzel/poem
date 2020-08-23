<?php

namespace Modules\Order;

use Poem\Data\Field;

class Model extends \Poem\Model 
{
    /**
     * Migration schema
     * 
     * @var array
     */
    const SCHEMA = [
        'id' => Field::PRIMARY_KEY,
        'state' => 'string',
        'user_id' => Field::FOREIGN_KEY
    ];
    
    /**
     * Initializes the order model
     * 
     * @return void
     */
    function initialize(): void
    {
        // An order has many items
        // $this->hasMany(['items' => 'order_items']);
            
        // An order belongs to a user
        // $this->belongsTo(['user' => 'users']);
    }
}
