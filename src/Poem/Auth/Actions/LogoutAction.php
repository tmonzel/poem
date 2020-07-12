<?php

namespace Poem\Auth\Actions;

use Poem\Actor\Action;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogoutAction extends Action {
    static $type = 'logout';

    function prepareData(Request $request) {
        
    }
}