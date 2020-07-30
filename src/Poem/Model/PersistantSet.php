<?php

namespace Poem\Model;

class PersistantSet extends Set {
    protected $subject;
    protected $conditions;
    protected $loaded = false;

    function __construct(string $subject, array $conditions = [])
    {
        $this->subject = $subject;
        $this->conditions = $conditions;
    }

    function load() {
        $this->subject::find($this->conditions);
        $this->loaded = true;
    }
}