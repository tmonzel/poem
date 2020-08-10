<?php

namespace Poem\Model\Relationships;

use Poem\Data\Statement;
use Poem\Model;
use Poem\Model\Accessor as ModelAccessor;
use Poem\Model\Collection;
use Poem\Model\Document;
use Poem\Model\FindQuery;

abstract class Relationship 
{
    use ModelAccessor;

    protected $options;
    protected $model;

    function __construct(Model $model, $options) 
    {
        $this->model = $model;
        $this->options = $options;
    }

    function getType() {
        return $this->getTargetModel()->getType();
    }

    function getName() {
        return $this->getTargetModel()->getName();
    }

    function getTargetModel(): Model {
        return static::Model()->access($this->options['target']);
    }

    function find(): FindQuery {
        return $this->getTargetModel()->find();
    }

    function pick(int $id): Document {
        return $this->getTargetModel()->pick($id);
    }

    function pickMany(array $ids): FindQuery {
        return $this->getTargetModel()->pickMany($ids);
    }

    function buildDocument(array $attributes = []) {
        return $this->getTargetModel()->buildDocument($attributes);
    }

    function getForeignKey(): string {
        return $this->getTargetModel()->foreignKey();
    }

    function save(Document $document) 
    {
        return $this->getTargetModel()->save($document, false);
    }

    function attachTo(Collection $collection, Statement $statement) {
        
    }

    function applyTo(Document $document, array $data) {
        
    }

    abstract function saveTo(Document $document): void;
}