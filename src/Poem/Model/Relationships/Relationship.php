<?php

namespace Poem\Model\Relationships;

abstract class Relationship {
    protected $subject;
    protected $relationName;

    function __construct($subject, $relationName) {
        $this->subject = $subject;
        $this->relationName = $relationName;
    }
}