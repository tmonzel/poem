<?php

namespace Poem\Auth;

class JwtToken {
    public $header;
    public $payload;
    public $signature;
    public $secret;

    static function generate(array $data)
    {
        $token = new static();
        $token->header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $token->payload = $data;

        return $token->tokenize();
    }

    function __construct()
    {
        /*[$header, $payload, $signature] = explode('.', trim($token));

        $this->header = $header;
        $this->payload = $payload;
        $this->signature = $signature;
        $this->secret = $secret;*/
    }

    function tokenize() 
    {
        $header = $this->encode($this->header);
        $payload = $this->encode($this->payload);

        return $header . "." . $payload . "." . $this->generateSignature($header, $payload);
    }

    function decodePayload(): array
    {
        // replace url-safe chars
        $base64Data = strtr($this->payload, '-_', '+/');

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
            $this->secret
        );

        return $this->encode($signature);
    }

    protected function encode($obj) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($obj));
    }
}