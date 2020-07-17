<?php

namespace Poem\Data;

interface CollectionAdapter
{
    function insert(array $document);
    function insertMany(array $documents);

    function findFirst(array $conditions = []);
    function findMany(array $conditions = []);

    function updateFirst(array $data, array $conditions = []);
    function updateMany(array $data, array $conditions = []);

    function deleteFirst(array $conditions = []);
    function deleteMany(array $conditions = []);
}
