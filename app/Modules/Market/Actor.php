<?php

namespace Modules\Market;

use Actions\CountAction;

class Actor extends \Poem\Actor
{
    /**
     * Initializes market actor
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->bind(static::RESOURCE_ACTIONS);
        $this->bind(CountAction::class);
    }
}
