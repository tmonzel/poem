<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Auth\Accessor as AuthAccessor;

class LoginAction extends Action {
    use AuthAccessor;

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

        // Create token via auth worker
        $token = static::Auth()->createTokenFor($user);

        return compact('token');
    }

    protected function verifyPassword(string $password, string $hash) {
        return password_verify($password, $hash);
    }
}
