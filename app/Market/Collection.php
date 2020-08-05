<?php

namespace Market;

class Collection extends \Poem\Model\Collection 
{
    /**
     * Market belongs to retailer
     * 
     * @var array
     */
    const BelongsTo = [
        'retailer'
    ];
}
