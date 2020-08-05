<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;

class CreateAction extends Action 
{
    use AttributeMapper;

    /**
     * Create action type
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
        if($this->payload->missing('attributes')) {
            throw new BadRequestException('No attributes found for create action');
        }

        $attributes = $this->map($this->payload->attributes);
        $document = $this->collection->new($attributes);

        /*if(method_exists($document, 'valid')) {
            if(!$document->valid()) {
                throw new BadRequestException(
                    'Validation errors', 
                    $document->validationErrors()
                );
            }
        }*/

        $this->collection->save($document);

        return $document;
    }
}
