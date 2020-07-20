<?php

namespace Poem\Actor;

use JsonSerializable;
use Poem\Actor;

class ActionStatement implements JsonSerializable {
    protected $actor;
    protected $actionClass;
    protected $payload = [];

    function __construct(Actor $actor, string $actionClass, $payload = []) 
    {
        $this->actor = $actor;
        $this->actionClass = $actionClass;
        $this->payload = $payload;
    }

    function execute() 
    {
        return $this->actor->dispatchAction($this->actionClass, $this->payload);
    }

    function jsonSerialize()
    {
        return $this->execute();
    }
}