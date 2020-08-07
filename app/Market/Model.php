<?php

namespace Market;

class Model extends \Poem\Model 
{
    /**
     * Market belongs to retailer
     * 
     * @var array
     */
    const BelongsTo = [
        'retailers'
    ];
}
