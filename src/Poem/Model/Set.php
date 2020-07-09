<?php

namespace Poem\Model;

use Poem\Set as BaseSet;

class Set extends BaseSet {
    function toRelatedData() {
        return $this->map(function($document) {
            return $document->toRelatedData();
        });
    }
}