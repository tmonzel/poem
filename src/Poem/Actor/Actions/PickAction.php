<?php

namespace Poem\Actor\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Poem\Actor\Action;
use Poem\Actor\Exceptions\NotFoundException;

class PickAction extends Action {
    static $method = 'get';
    static $route = '/{id}';

    function prepareData(Request $request) {
        $id = $request->getAttribute('id');
        $document = $this->subjectClass::pick($id);

        if(!$document) {
            // Document does not exist - throw error
            throw new NotFoundException('Resource not found');
        }

        return $document;
    }
}