<?php

namespace Poem\Auth;

use Poem\Behavior;
use Poem\Model;

/**
 * Model behavior
 */
class BehaveAsUser extends Behavior 
{
    function initialize() 
    {
        $this->shouldActOn(Model::BEFORE_SAVE_EVENT, [$this, 'beforeSave']);
    }

    function beforeSave(Model $user, array $payload = []) 
    {
        if(isset($user->password)) {
            $user->password = password_hash($user->password, PASSWORD_ARGON2I);
        }
    }
}
