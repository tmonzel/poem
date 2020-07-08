<?php

namespace Poem\Model\Relationships;

abstract class Relationship {
    protected $relationName;

    function __construct($modelClass, $relationName) {
        $this->relationName = $relationName;
    }
}