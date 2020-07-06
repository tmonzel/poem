<?php

namespace Poem\Actor\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Model;

class UpdateAction extends Action {
    use AttributeMapper;

    static $method = 'patch';
    static $route = '/{id}';

    function prepareData(Request $request) {
        $id = $request->getAttribute('id');
        $attributes = $request->getParsedBody();
        
        /** @var Model $document */
        $document = $this->subjectClass::pick($id);
        $document->writeAttributes($this->map($attributes));
        $document->save();
        
        return $document;
    }
}