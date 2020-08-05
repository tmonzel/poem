<?php

namespace Poem\Data;

use Iterator;
use JsonSerializable;

interface Cursor extends Iterator, JsonSerializable 
{
    function map(callable $mapper);
}