<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Model;

class UpdateAction extends Action {
    use AttributeMapper;

    static $type = 'update';

    function prepareData() 
    {
        if(!isset($this->payload['id'])) {
            throw new BadRequestException('id must be provided in payload');
        }

        if(!isset($this->payload['data'])) {
            throw new BadRequestException('data must be provided in payload');
        }

        $id = $this->payload['id'];
        $attributes = $this->payload['data'];
        
        /** @var Model $document */
        $document = $this->subject::pick($id);
        $document->writeAttributes($this->map($attributes));
        $document->save();
        
        return $document;
    }
}
