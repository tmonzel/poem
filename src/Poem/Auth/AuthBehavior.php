<?php

namespace Poem\Auth;

use Poem\Actor\Action;
use Poem\Actor\Behavior;

/**
 * Actor behavior
 */
class AuthBehavior extends Behavior 
{
    function encodePassword($password) {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    function prepareAction(Action $action) 
    {
        $payload = $action->getPayload();

        switch($action->getType()) {
            case 'create':
                // Encode password
                $payload['password'] = $this->encodePassword($payload['password']);
            break;
        }

        $action->setPayload($payload);
    }
}