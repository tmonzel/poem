<?php

namespace Poem\Model\Relationships;

use Poem\Model;

/**
 * Proxy for has many relations
 */
class HasManyRelationship extends Relationship {
    function connect(Model $model) {
        $result = $this->subject::find([$model->foreignKey() => (int)$model->id]);
        $model->setRelation($this->relationName, $result);
        return $result;
    }
}