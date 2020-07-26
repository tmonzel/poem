<?php

namespace Poem;

use Symfony\Component\HttpFoundation\Request;

interface RequestHandler {
    function handleRequest(Request $request);
}