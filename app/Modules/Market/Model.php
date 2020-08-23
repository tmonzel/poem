<?php

namespace Modules\Market;

class Model extends \Poem\Model
{
    /**
     * Initializes market model
     * 
     * @return void
     */
    function initialize(): void
    {
        // A Market belongs to a retailer
        $this->belongsTo([
            'retailer' => 'retailers'
        ]);
    }
}
