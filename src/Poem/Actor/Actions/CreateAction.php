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
        
        if(!$this->collection->save($document)) {
            if($document->hasErrors()) {
                throw new BadRequestException(
                    'Validation errors', 
                    $document->getErrors()
                );
            }
        }

        return $document;
    }
}
