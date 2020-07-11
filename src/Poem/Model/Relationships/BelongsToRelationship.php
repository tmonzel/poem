<?php

namespace Poem\Model\Relationships;

use Poem\Model;

/**
 * Proxy for belongs to relations
 */
class BelongsToRelationship extends Relationship {
    function connect(Model $model) {
        $fk = $this->subject::foreignKey();
        $relatedId = (int)$model->{$fk};

        if($relatedId) {
            $result = $this->subject::pick($relatedId);

            if($result) {
                $model->setRelation($this->relationName, $result);
            }
        }

        return $result;
    }
}