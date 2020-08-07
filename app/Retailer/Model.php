<?php

namespace Retailer;

class Model extends \Poem\Model
{    
    /**
     * Database schema needed for migrations
     * 
     * @var array
     */
    const Schema = [
        'id' => 'pk',
        'name' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];
    
    /**
     * A retailer has many markets
     * 
     * @var array
     */
    const HasMany = [
        'markets'
    ];
}