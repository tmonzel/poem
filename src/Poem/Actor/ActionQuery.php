<?php

namespace Poem\Actor;

class ActionQuery {
    protected $type;
    protected $payload;
    protected $headers;
    
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