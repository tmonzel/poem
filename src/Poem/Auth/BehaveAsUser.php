<?php

namespace Poem\Auth;

use Poem\Behavior;
use Poem\Model\Collection;

/**
 * Collection behavior
 */
class BehaveAsUser extends Behavior 
{
    function initialize() 
    {
        $this->shouldActOn(Collection::BEFORE_SAVE_EVENT, [$this, 'beforeSave']);
    }

    function beforeSave(Collection $collection, array $payload = []) 
    {
        [$user] = $payload;
        
        if(isset($user->password)) {
            $user->password = password_hash($user->password, PASSWORD_ARGON2I);
        }
    }
}
