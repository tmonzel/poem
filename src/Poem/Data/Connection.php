<?php

namespace Poem\Data;

interface Connection 
{
    function connect(array $config);
    function accessAdapter(string $type): CollectionAdapter;
}