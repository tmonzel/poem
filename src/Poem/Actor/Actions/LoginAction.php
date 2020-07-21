<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\BadRequestException;

class LoginAction extends Action {
    static $type = 'login';
    
    function prepareData() 
    {
        // Test username and password against user model
        // Generate a jwt token for authorized users
        // Return 403 on error

        $data = $this->payload;

        $user = $this->subject::first([
            'name' => $data['name'],
        ]);

        if(!$user) {
            // error
            throw new BadRequestException('User not found');
        }

        if(!$this->verifyPassword($data['password'], $user->password)) {
            throw new BadRequestException('Invalid login');
        }

        // Generate Token
        $token = $this->auth->createTokenFor($user);

        return compact('token');
    }

    protected function verifyPassword(string $password, string $hash) {
        return password_verify($password, $hash);
    }
}
