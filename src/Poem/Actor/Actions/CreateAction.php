<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;

class CreateAction extends Action {
    use AttributeMapper;

    static $type = 'create';

    function prepareData() 
    {
        $attributes = $this->payload;
        $document = new $this->subject($this->map($attributes));

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
