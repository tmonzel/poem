<?php

namespace Modules\Product;

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
        'name' => 'string',
    ];
    
    /**
     * Initialize product model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->validateAttribute('name', 'required');
    }
}
