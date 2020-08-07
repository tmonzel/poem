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

        // Access the related model
        $model = $this->actor->accessModel();

        $document = $model->new(
            $this->map($this->payload->attributes)
        );
        
        if(!$model->save($document)) {
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
