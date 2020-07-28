<?php

namespace Poem\Model\Relationships;

use Poem\Model;

abstract class Relationship {
    protected $subject;
    protected $relationName;

    function __construct($subject, $relationName) {
        $this->subject = $subject;
        $this->relationName = $relationName;
    }

    function connect(Model $model) {
        
    }
}