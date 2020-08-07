<?php

namespace Poem\Data;

use Iterator;
use JsonSerializable;

interface Statement extends Iterator, JsonSerializable 
{
    function addMapper(callable $mapper);
}