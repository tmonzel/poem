<?php

namespace Product;
use Poem\Actor\Actions\FindAction;
class Actor extends \Poem\Actor 
{
    /**
     * Product actor type definition
     * 
     * @var string
     */
    const Type = 'products';

    /**
     * Available actions for this actor
     * 
     * @var array
     */
    const Actions = [
        FindAction::class
    ];
}
