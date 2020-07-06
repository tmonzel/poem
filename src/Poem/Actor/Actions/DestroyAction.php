<?php

namespace Poem\Actor\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;

class DestroyAction extends DeleteAction {
    static $route = '/{id}';

    function prepareData(Request $request) {
        $id = $request->getAttribute('id');

        $document = $this->subjectClass::pick($id);

        if(!$document) {
            // Not found
            return ['errors' => 'Document not found'];
        }

        $document->destroy();

        return $document;
    }
}