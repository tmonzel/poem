<?php

namespace Poem\Data;

interface Connection {
    function accessCollection($name): Collection;
    function connect(array $config);
}