<?php

namespace Modules\Order;

use Actions\CountAction;

class Actor extends \Poem\Actor
{
    /**
     * Initialize order actor
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->bind(static::RESOURCE_ACTIONS);
        $this->bind(CountAction::class);
    }
}