<?php

namespace Poem\Auth;

use Poem\Model;
use Poem\RequestHandler;
use Symfony\Component\HttpFoundation\Request;

class Worker implements RequestHandler
{
    const Accessor = 'auth';

    /**
     * Create authorized user from this class
     * 
     * @var string
     */
    protected $userSubject = 'User\\Model';

    protected $token;
    protected $user;

    function handleRequest(Request $request) {
        $headers = $request->headers->all();
        
        if(isset($headers['authorization']) && isset($headers['authorization'][0])) {
            $token = $headers['authorization'][0];
            $this->setToken($token);
            
        }
    }

    function setUserSubject(string $userSubject) 
    {
        $this->userSubject = $userSubject;
    }

    function setToken(string $token) 
    {
        $this->token = $token;
    }

    function authorized($role = null) 
    {
        return $this->user() !== null;
    }

    /**
     * @return Model
     */
    function user() 
    {
        if(!$this->user) {
            $this->user = $this->resolveUser();
        }

        return $this->user;
    }

    function createTokenFor(Model $user) 
    {
        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode(['userId' => $user->id]);

        $encodedHeader = $this->encode($header);
        $encodedPayload = $this->encode($payload);
        $signature = $this->generateSignature($encodedHeader, $encodedPayload);

        return $encodedHeader . "." . $encodedPayload . "." . $signature;
    }

    protected function resolveUser() 
    {
        if(empty($this->token) || substr_count($this->token, '.') !== 2) {
            return;
        }
        
        $token = preg_replace('/^Bearer /', '', $this->token);

        [$header, $payload, $signature] = explode('.', trim($token));
 
        if($signature === $this->generateSignature($header, $payload)) {
            $data = $this->decodePayload($payload);
            
            if(isset($data['userId'])) {
                $user = $this->userSubject::pick($data['userId']);
                
                if($user) {
                    // User found and set
                    return $user;
                }
            }
        }
    }

    private function decodePayload(string $payload): array
    {
        // replace url-safe chars
        $base64Data = strtr($payload, '-_', '+/');

        // decode array
        return (array)json_decode(
            base64_decode($base64Data, false), 
            false
        );
    }
    
    /**
     * 
     */
    private function generateSignature(string $header, string $payload): string
    {
        $signature = hash_hmac(
            'sha256',
            sprintf('%s.%s', $header, $payload),
            getenv('AUTH_SECRET')
        );

        return $this->encode($signature);
    }

    /**
     * Encode to base64
     * 
     * @param string $data
     * @return string
     */
    private function encode(string $data) 
    {
        return str_replace(
            ['+', '/', '='], 
            ['-', '_', ''], 
            base64_encode($data)
        );
    }
}