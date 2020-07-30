<?php

namespace Poem\Model;

use Poem\Model;

class RelatedSet extends PersistantSet {
    protected $relatedDocument;

    function __construct(string $subject, Model $relatedDocument)
    {
        parent::__construct($subject, [$relatedDocument->foreignKey() => $relatedDocument->id]);
        $this->relatedDocument = $relatedDocument;
    }

    function create(array $attributes = []) 
    {
        $this->subject::create($this->conditions + $attributes);
    }
}