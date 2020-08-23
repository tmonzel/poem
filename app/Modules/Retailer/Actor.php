<?php

namespace Modules\Retailer;

class Actor extends \Poem\Actor
{
    /**
     * Initialize retailer actor
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->bind(Actor::RESOURCE_ACTIONS);
        $this->guardActions();
    }
}
