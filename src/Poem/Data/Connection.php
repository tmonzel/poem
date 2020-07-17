<?php

namespace Poem\Data;

interface Connection {
    function createCollection($name, array $schema = null);
    function accessCollection($name): Collection;
    function connect(array $config);
}