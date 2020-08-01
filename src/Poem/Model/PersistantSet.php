<?php

namespace Poem\Model;

class PersistantSet extends Set 
{
    protected $subject;
    protected $conditions;
    protected $loaded = false;

    function __construct(string $subject, array $conditions = [])
    {
        $this->subject = $subject;
        $this->conditions = $conditions;
    }

    function load() 
    {
        if(!$this->loaded) {
            $documents = $this->subject::find($this->conditions);
            $this->fill($documents->all());
            $this->loaded = true;
        }
    }

    function new(array $items = [])
    {
        $set = new static($this->subject, $this->conditions);
        $set->fill($items);
        return $set;
    }

    function toRelatedData()
    {
        $this->load();
        return parent::toRelatedData();
    }
}