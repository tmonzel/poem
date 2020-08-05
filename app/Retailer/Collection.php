<?php

namespace Retailer;

class Collection extends \Poem\Model\Collection 
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