<?php

namespace Poem\Actor\Actions;

use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateAction extends PostAction {
    use AttributeMapper;

    function prepareData(Request $request) {
        $attributes = $request->getParsedBody();
        $document = new $this->subjectClass($this->map($attributes));

        if(method_exists($document, 'valid')) {

            if(!$document->valid()) {
                throw new BadRequestException(
                    'Validation errors', 
                    $document->validationErrors()
                );
            }
        }

        $document->save();

        return $document;
    }
}