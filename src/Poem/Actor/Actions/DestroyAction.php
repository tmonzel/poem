<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;

class DestroyAction extends Action 
{
    /**
     * Action type definition
     * 
     * @static
     * @var string
     */
    static $type = 'destroy';

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

        $document = $this->collection->pick($this->payload->id);

        if(!$document) {
            throw new NotFoundException('Document not found');
        }

        $this->collection->destroy($document);

        return $document;
    }
}
