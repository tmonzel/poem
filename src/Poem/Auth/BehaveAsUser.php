<?php

namespace Poem\Auth;

use Poem\Behavior;
use Poem\Model;

/**
 * Model behavior
 */
class BehaveAsUser extends Behavior {
    function initialize() 
    {
        $this->shouldActOn('model_map_attributes', [$this, 'mapAttributes']);
    }

    function mapAttributes(Model $user, array $payload) 
    {
        if(isset($payload['password'])) {
            $payload['password'] = password_hash($payload['password'], PASSWORD_ARGON2I);
        }

        return $payload;
    }
}