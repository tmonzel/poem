<?php

namespace Poem\Model\Relationships;

use Poem\Model;

/**
 * Proxy for belongs to relations
 */
class BelongsToRelationship extends Relationship {
    function connect(Model $model) {
        $result = [];

        $model->setRelation($this->relationName, $result);
    }
}