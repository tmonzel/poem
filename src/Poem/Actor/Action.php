<?php

namespace Poem\Actor;

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
     * Action payload
     * 
     * @var Bag 
     */
    protected $payload;


    /**
     * Applied collection instance
     * 
     * @var Collection
     */
    protected $collection;

    /**
     * Creates a new action.
     * 
     */
    function __construct()
    {
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
     * Apply a collection for this action
     * 
     * @param Collection $collection
     * @return void
     */
    function setCollection(Collection $collection): void 
    {
        $this->collection = $collection;
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