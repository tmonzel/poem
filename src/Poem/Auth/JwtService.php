<?php

namespace Poem\Auth;

class JwtService {
    const TOKEN_SECRET = 'n239hfe23rhndqoahie';

    function getSecret(): string 
    {
        return self::TOKEN_SECRET;
    }

    function generateToken(array $data) 
    {
        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode($data);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        // Create Signature Hash
        $base64UrlSignature = $this->generateSignature($base64UrlHeader, $base64UrlPayload);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Returns false or payload
     */
    function validateToken(string $token) 
    {
        [$header, $payload, $signature] = explode('.', trim($token));
        
        if($signature === $this->generateSignature($header, $payload)) {
            $payloadData = $this->decodePayload($payload);
            
            // Identify and set user
            return $payloadData;
        }

        return false;
    }

    private function decodePayload(string $payload) 
    {
        // replace url-safe chars
        $base64Data = strtr($payload, '-_', '+/');

        // decode array
        return (array)json_decode(
            base64_decode($base64Data, false), false
        );
    }

    /**
     * 
     */
    private function generateSignature($header, $payload): string
    {
        $signature = hash_hmac(
            'sha256',
            sprintf('%s.%s', $header, $payload),
            $this->getSecret()
        );

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    }
}