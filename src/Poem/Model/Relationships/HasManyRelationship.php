<?php

namespace Poem\Model\Relationships;

use Poem\Model;
use Poem\Model\PersistantSet;

/**
 * Proxy for has many relations
 */
class HasManyRelationship extends Relationship {
    function connect(Model $model) {
        $set = new PersistantSet($this->subject, [$model->foreignKey() => (int)$model->id]);
        $model->setRelation($this->relationName, $set);
        return $set;
    }
}