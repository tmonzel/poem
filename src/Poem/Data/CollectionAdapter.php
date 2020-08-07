<?php

namespace Poem\Data;

interface CollectionAdapter
{
    function find(array $filter = [], array $options = []): Statement;
    function insert(array $data, array $options = []);
    function delete(array $filter, array $options = []);
    function update(array $filter, array $data, array $options = []);
    function migrate(array $schema): void;
}
