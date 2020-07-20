<?php

namespace Poem;

use Poem\Actor\Exceptions\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class Query {
    protected $request;
    protected $data;

    /**
     * Authorization handler
     * 
     * @var Auth
     */
    protected $auth;

    function __construct(Request $request) 
    {
        $this->request = $request;
        $this->auth = new Auth();
    }

    function compile() 
    {
        $headers = $this->getHeaders();

        if(isset($headers['authorization']) && isset($headers['authorization'][0])) {
            $token = $headers['authorization'][0];
            $this->auth->setToken($token);
        }

        $this->data = $this->encodeRequestBody();

        if(!$this->data) {
            throw new BadRequestException('Invalid query. Please provide valid json format');
        }
    }

    function getData() 
    {
        return $this->data;
    }

    function getAuth(): Auth 
    {
        return $this->auth;
    }

    function getHeaders(): array
    {
        return $this->request->headers->all();
    }

    /**
     * 
     */
    private function encodeRequestBody(): array
    {
        $rawBody = $this->request->getContent();
        return $rawBody ? json_decode($rawBody, true) : [];
    }
}