<?php

namespace Poem\Auth\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Auth\Accessor as AuthAccessor;

class LoginAction extends Action 
{
    use AuthAccessor;

    static $type = 'login';
    
    function prepareData() 
    {
        // Test username and password against user model
        // Generate a jwt token for authorized users
        // Return 403 on error

        $payload = $this->payload;

        if($this->payload->missing('username')) {
            throw new BadRequestException('Missing `username` parameter in payload');
        }

        if($this->payload->missing('password')) {
            throw new BadRequestException('Missing `password` parameter in payload');
        }

        // Access the related model
        $model = $this->actor->getModel();

        $user = $model->first([
            'name' => $this->payload->username,
        ]);

        if(!$user) {
            throw new BadRequestException('User not found');
        }

        if(!$this->verifyPassword($this->payload->password, $user->password)) {
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
