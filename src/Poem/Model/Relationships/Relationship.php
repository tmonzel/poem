<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model\Accessor as ModelAccessor;
use Poem\Model\Collection;
use Poem\Model\FindQuery;

abstract class Relationship 
{
    use ModelAccessor;

    protected $options;

    function __construct($options) {
        $this->options = $options;
    }

    function getTargetCollection(): Collection {
        return static::Model()->access($this->options['target']);
    }

    function find(): FindQuery {
        return $this->getTargetCollection()->find();
    }

    function getForeignKey(): string {
        return $this->getTargetCollection()->foreignKey();
    }

    function attachTo(Collection $collection, Statement $statement) {
        
    }
}