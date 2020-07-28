<?php

namespace Poem\Actor;

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
     * Action subject
     * Typically a model class
     * 
     * @var mixed
     */
    protected $subject;

    /**
     * Action payload
     * 
     * @var array 
     */
    protected $payload = [];

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
     * Set the action subject
     * 
     * @param mixed subject
     */
    function setSubject($subject) 
    {
        $this->subject = $subject;
    }

    /**
     * Set the action payload
     * 
     * @param array $payload
     */
    function setPayload(array $payload) 
    {
        $this->payload = $payload;
    }

    /**
     * Return the action payload
     * 
     * @return array
     */
    function getPayload(): array 
    {
        return $this->payload;
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