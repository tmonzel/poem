<?php

namespace Poem\Actor;

use JsonSerializable;
use Poem\Actor;

class ActionQuery implements JsonSerializable 
{
    /**
     * Owning actor instance
     * 
     * @var Actor
     */
    protected $actor;

    /**
     * Action type
     * 
     * @var string
     */
    protected $actionType;

    /**
     * Action payload
     * 
     * @var array
     */
    protected $payload = [];

    /**
     * 
     * @param Actor $actor
     * @param string $actionType
     * @param array $payload
     */
    function __construct(Actor $actor, string $actionType, array $payload = []) 
    {
        $this->actor = $actor;
        $this->actionType = $actionType;
        $this->payload = $payload;
    }

    /**
     * Returns the action type.
     * 
     * @return string
     */
    function getType(): string
    {
        return $this->actionType;
    }

    /**
     * Returns the action payload.
     * 
     * @return array
     */
    function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Execute this action
     * 
     * @return mixed
     */
    function execute() 
    {
        return $this->actor->execute($this);
    }

    /**
     * Serialize to json
     * 
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this->execute();
    }
}
