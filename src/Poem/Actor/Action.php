<?php

namespace Poem\Actor;

use Poem\Auth;

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
     * User auth instance
     * 
     * @var Auth
     */
    protected $auth;

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
    function getPayload(): array {
        return $this->payload;
    }

    /**
     * Set the authentication object
     * 
     * @param Auth $auth
     */
    function setAuth(Auth $auth) 
    {
        $this->auth = $auth;
    }

    /**
     * Execute the action
     * 
     * @return mixed
     */
    function dispatch() 
    {
        return $this->prepareData();
    }

    function prepareData() {
        
    }
}