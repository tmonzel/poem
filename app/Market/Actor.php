<?php

namespace Market;

use Poem\Actor\BehaveAsResource;

class Actor extends \Poem\Actor 
{
    /**
     * Market actor type definition
     * 
     * @var string
     */
    const Type = 'markets';

    /**
     * Registered market actor behaviors
     * 
     * @var array
     */
    const Behaviors = [
        BehaveAsResource::class,
    ];

    /**
     * Market belongs to retailer
     * 
     * @var array
     */
    const BelongsTo = [
        'retailer' => 'retailers'
    ];
}
