<?php

namespace Poem\Data;

use Poem\Set;

abstract class Collection {
    protected $name;

    function __construct($name) {
        $this->name = $name;
    }

    function insert(array $document) {
        
    }

    function insertMany(array $documents) {

    }

    function findFirst(array $conditions = []) {
        
    }

    abstract function findMany($conditions = []): Set;

    function updateFirst(array $data, array $conditions = []) {

    }

    function updateMany($data, $conditions = []) {

    }

    function deleteFirst(array $conditions = []) {

    }

    function deleteMany($conditions = []) {
        
    }
}