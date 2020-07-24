<?php

namespace Poem;

use Symfony\Component\HttpFoundation\Request;

interface Worker {
    function initialize(Request $request);
}