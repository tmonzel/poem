<?php

namespace Poem\Actor;

class ActionQuery {
    protected $type;
    protected $payload;
    
    function __construct(string $type, array $payload = []) {
        $this->type = $type;
        $this->payload = $payload;
    }

    function getType(): string {
        return $this->type;
    }

    function getPayload(): array {
        return $this->payload;
    }
}