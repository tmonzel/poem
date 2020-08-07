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
     * Product validations forced by Validateable
     * 
     * @return array
     */
    function validations(): array 
    {
        return [
            'name' => ['required']
        ];
    }
}
