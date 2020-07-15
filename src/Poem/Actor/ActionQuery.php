<?php

namespace Poem\Actor;

use Poem\Auth;

class ActionQuery {
    protected $type;
    protected $payload;
    protected $headers;

    /**
     * Optional auth if auth header is set
     * 
     * @var Auth
     */
    public $auth;

    function __construct(string $type, array $payload = [], array $headers = []) {
        $this->type = $type;
        $this->payload = $payload;
        $this->headers = $headers;
    }

    function getType(): string {
        return $this->type;
    }

    function getPayload(): array {
        return $this->payload;
    }

    function getHeaders(): array {
        return $this->headers;
    }
}