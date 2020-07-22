<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;

class CreateAction extends Action {
    use AttributeMapper;

    /**
     * Create action type (required)
     * 
     * @static
     * @var string
     */
    static $type = 'create';

    /**
     * Prepare data for execution
     * 
     * @return mixed
     */
    function prepareData() 
    {
        if(!isset($this->payload['attributes'])) {
            throw new BadRequestException('No attributes found for create action');
        }

        $attributes = $this->payload['attributes'];
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

        return $document->toData($this->payload);
    }
}
