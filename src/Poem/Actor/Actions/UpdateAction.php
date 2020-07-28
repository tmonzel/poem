<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\AttributeMapper;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Model;

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
     * Prepare action data
     * 
     * @return mixed
     */
    function prepareData() 
    {
        if(!isset($this->payload['id'])) {
            throw new BadRequestException('id must be provided in payload');
        }

        $id = $this->payload['id'];
        
        /** @var Model $document */
        $document = $this->subject::pick($id);

        if(isset($this->payload['relationships'])) {
            foreach($this->payload['relationships'] as $name => $data) {
                $relationship = $document->getConnectedRelationship($name);
            }
        }
        
        if(!$document) {
            throw new NotFoundException("Document with #$id not found");
        }
        
        if(isset($this->payload['attributes'])) {
            $document->writeAttributes(
                $this->map($this->payload['attributes'])
            );
        }

        $document->save();
        
        return $document->toData($this->payload);
    }
}
