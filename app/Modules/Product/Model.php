<?php

namespace Modules\Product;

class Model extends \Poem\Model
{
    /**
     * Initialize product model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->setSchema([
            'id' => 'pk',
            'name' => 'string',
        ]);
        
        $this->validateAttribute('name', 'required');
    }
}
