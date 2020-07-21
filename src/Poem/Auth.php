<?php

namespace Poem;

class Auth 
{
    /**
     * Create authorized user from this class
     * 
     * @var string
     */
    static $userModel = 'User\\Model';

    protected $token;
    protected $user;

    function setToken(string $token) {
        $this->token = $token;
    }

    function authorized($role = null) 
    {
        return $this->user() !== null;
    }

    function resolveUser() 
    {
        if(empty($this->token)) {
            return;
        }
        
        $token = preg_replace('/^Bearer /', '', $this->token);

        [$header, $payload, $signature] = explode('.', trim($token));

        if($signature === static::generateSignature($header, $payload)) {
            $data = static::decodePayload($payload);
            
            if(isset($data['userId'])) {
                $user = static::$userModel::pick($data['userId']);
                
                if($user) {
                    // User found and set
                    return $user;
                }
            }
        }
    }

    // Identify the user by token or false
    function user() 
    {
        if(!$this->user) {
            $this->user = $this->resolveUser();
        }

        return $this->user;
    }

    static function decodePayload(string $payload): array
    {
        // replace url-safe chars
        $base64Data = strtr($payload, '-_', '+/');

        // decode array
        return (array)json_decode(
            base64_decode($base64Data, false), false
        );
    }

    static function createTokenFor(Model $user) 
    {
        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode(['userId' => $user->id]);

        $encodedHeader = static::encode($header);
        $encodedPayload = static::encode($payload);
        $signature = static::generateSignature($encodedHeader, $encodedPayload);

        return $encodedHeader . "." . $encodedPayload . "." . $signature;
    }
    
    /**
     * 
     */
    private static function generateSignature($header, $payload): string
    {
        $signature = hash_hmac(
            'sha256',
            sprintf('%s.%s', $header, $payload),
            getenv('AUTH_SECRET')
        );

        return static::encode($signature);
    }

    private static function encode($obj) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($obj));
    }
}