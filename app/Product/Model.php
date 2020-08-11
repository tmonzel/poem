<?php

namespace Product;

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
    ];

    /**
     * Initialize product model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->addValidation('name', 'reqired');
    }
}
