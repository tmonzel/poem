<?php

namespace Modules\Product;

use Actions\CountAction;
use Poem\Actor\Actions\FindAction;

class Actor extends \Poem\Actor
{
    /**
     * Initialize product actor
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->bind(FindAction::class);
        $this->bind(CountAction::class);
    }
}