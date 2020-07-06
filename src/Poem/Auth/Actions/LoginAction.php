<?php

namespace Poem\Auth\Actions;

use Poem\Actor\Action;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction extends Action {
    static $method = 'post';
    static $route = '/login';
    
    function prepareData(Request $request) {
        // Test username and password against user model
        // Generate a jwt token for authorized users
        // Return 403 on error

        $data = $request->getParsedBody();

        $user = $this->subjectClass::find([
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