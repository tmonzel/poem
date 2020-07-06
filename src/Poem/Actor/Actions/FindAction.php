<?php

namespace Poem\Actor\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Poem\Actor\Action;

class FindAction extends Action {
    static $method = 'get';

    function prepareData(Request $request) {
        return $this->subjectClass::find();
    }
}