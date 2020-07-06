<?php

namespace Retailer  {

    use Poem\Actor\ResourceBehavior;

    class Actor extends \Poem\Actor {
        static $Behaviors = [
            ResourceBehavior::class
        ];
    }
}