<?php

namespace Poem\Data;

interface Client {
    function getCollection($name): Collection;
}