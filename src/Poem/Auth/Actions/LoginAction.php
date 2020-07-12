<?php

namespace Poem\Auth\Actions;

use Poem\Actor\Action;

class LoginAction extends Action {
    static $type = 'login';
    
    function prepareData() {
        // Test username and password against user model
        // Generate a jwt token for authorized users
        // Return 403 on error

        $data = $this->payload;

        $user = $this->subject::find([
            'name' => $data['name'],
            'password' => $data['password']
        ]);

        if(!$user) {
            // error
            return;
        }

        // Generate Token
        return $user;
    }
}