<?php

namespace Poem\Data;

interface Connection {
    function connect(array $config);
    function getCollectionAdapter(string $name): CollectionAdapter;
}