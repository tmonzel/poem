<?php

namespace Product;
use Poem\Model\Validateable;

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
