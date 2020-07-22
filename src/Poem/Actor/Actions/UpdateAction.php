<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Model;

class UpdateAction extends Action {
    use AttributeMapper;

    static $type = 'update';

    function prepareData() 
    {
        if(!isset($this->payload['id'])) {
            throw new BadRequestException('id must be provided in payload');
        }

        if(!isset($this->payload['attributes'])) {
            throw new BadRequestException('Attributes must be provided in payload');
        }

        $id = $this->payload['id'];
        $attributes = $this->payload['attributes'];
        
        /** @var Model $document */
        $document = $this->subject::pick($id);
        
        if(!$document) {
            throw new NotFoundException("Document with #$id not found");
        }
        
        $document->writeAttributes($this->map($attributes));
        $document->save();
        
        return $document->toData($this->payload);
    }
}
