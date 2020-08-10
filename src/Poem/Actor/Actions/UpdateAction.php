<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;

class UpdateAction extends Action 
{
    use AttributeMapper;

    /**
     * Update action type
     * 
     * @static
     * @var string
     */
    static $type = 'update';

    /**
     * Prepare data for execution
     * 
     * @return mixed
     */
    function prepareData() 
    {
        if($this->payload->missing('id')) {
            throw new BadRequestException('id must be provided in payload');
        }

        // Access the related model
        $model = $this->actor->accessModel();

        $document = $model->pick($this->payload->id, ['include' => '*']);
        
        if(!$document) {
            throw new NotFoundException('Document#' . $this->payload->id . ' not found');
        }

        // Apply all relationships data to the document
        if($this->payload->present('relationships')) {
            foreach($this->payload->relationships as $name => $data) {
                if(!$model->hasRelationship($name)) {
                    continue;
                }

                $relationship = $model->getRelationship($name);
                $relationship->applyTo($document, $data);
            }
        }
        
        if($this->payload->present('attributes')) {
            $document->fill(
                $this->map($this->payload->attributes)
            );
        }

        $model->save($document);
        
        return $document;
    }
}
