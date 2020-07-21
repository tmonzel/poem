<?php

namespace Poem\Actor;

use JsonSerializable;
use Poem\Actor;

class ActionStatement implements JsonSerializable {
    protected $actor;
    protected $actionType;
    protected $payload = [];

    function __construct(Actor $actor, string $actionType, $payload = []) 
    {
        $this->actor = $actor;
        $this->actionType = $actionType;
        $this->payload = $payload;
    }

    function execute() 
    {
        return $this->actor->dispatchAction($this->actionType, $this->payload);
    }

    function jsonSerialize()
    {
        return $this->execute();
    }
}