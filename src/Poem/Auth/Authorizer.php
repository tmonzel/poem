<?php

namespace Poem\Auth;

class Authorizer 
{
    protected $user;
    protected $subject;
    protected $jwt;

    function __construct($subject = 'User\\Model') 
    {
        $this->subject = $subject;
        $this->jwt = new JwtService();
    }

    function authorize(string $token) 
    {
        // Set user or return false
        $token = preg_replace('/^Bearer /', '', $token);
        $payload = $this->jwt->validateToken($token);

        if($payload === false) {
            return false;
        }

        // $userId = $payload['userId'];

        return true;
    }
}