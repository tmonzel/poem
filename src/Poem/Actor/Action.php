<?php

namespace Poem\Actor;

use Poem\Actor;
use Poem\Bag;
use Poem\Model\Collection;

abstract class Action 
{
    /**
     * Action type definiton
     * Every action must have a type
     * @TODO: Force inherited classes to defined abstract getType
     * 
     * @static
     * @var string
     */
    static $type;

    /**
     * Executing actor instance
     * 
     * @var Actor
     */
    protected $actor;

    /**
     * Action payload
     * 
     * @var Bag 
     */
    protected $payload;

    /**
     * Creates a new action.
     * 
     * @param Actor $actor
     */
    function __construct(Actor $actor)
    {
        $this->actor = $actor;
        $this->payload = new Bag();
    }

    /**
     * Provide the action type definition
     * 
     * @static
     * @return string
     */
    static function getType(): string 
    {
        return static::$type;
    }

    /**
     * Set the action payload
     * 
     * @param array $data
     * @return void
     */
    function setPayload(array $data): void 
    {
        $this->payload = new Bag($data);
    }

    /**
     * Execute the action
     * 
     * @return mixed
     */
    function execute() 
    {
        return $this->prepareData();
    }

    /**
     * Prepare data for execution
     * 
     * @return mixed
     */
    abstract function prepareData();
}