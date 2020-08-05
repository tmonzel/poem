<?php

namespace Poem\Auth;

use Poem\Behavior;
use Poem\Model\Collection;
use Poem\Model\Document;

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
        /** @var Document $user */
        $user = $payload[0];
        
        if($user->isDirty('password')) {
            $user->password = password_hash($user->password, PASSWORD_ARGON2I);
        }
    }
}
