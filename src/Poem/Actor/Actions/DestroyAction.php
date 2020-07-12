<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;

class DestroyAction extends Action {
    static $type = 'destroy';

    function prepareData() 
    {
        if(!isset($this->payload['id'])) {
            throw new BadRequestException('id must be provided in payload');
        }

        $id = $this->payload['id'];
        $document = $this->subject::pick($id);

        if(!$document) {
            throw new NotFoundException('Document not found');
        }

        $document->destroy();

        return $document;
    }
}
