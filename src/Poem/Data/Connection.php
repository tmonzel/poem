<?php

namespace Poem\Data;

interface Connection {
    function createCollection($name, array $schema = null);
    function accessCollection($name): CollectionAdapter;
    function connect(array $config);
}