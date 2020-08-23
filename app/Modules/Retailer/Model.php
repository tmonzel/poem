<?php

namespace Modules\Retailer;

use Poem\Data\Field;

class Model extends \Poem\Model
{
    const SCHEMA = [
        'id' => Field::PRIMARY_KEY,
        'name' => 'string',
        'created_at' => 'date',
        'updated_at' => 'date'
    ];
    
    function initialize(): void
    {
        // A Retailer has many markets
        $this->hasMany('markets');
    }
}
