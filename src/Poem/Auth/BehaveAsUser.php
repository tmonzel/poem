<?php

namespace Poem\Auth;

use Poem\Behavior;

/**
 * Model behavior
 */
class BehaveAsUser extends Behavior {
    function initialize() 
    {
        $this->shouldActOn('model_map_attributes', [$this, 'mapAttributes']);
    }

    function mapAttributes(object $subject, array $payload) 
    {

    }
}