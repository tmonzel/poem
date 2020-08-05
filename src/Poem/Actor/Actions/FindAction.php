<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\NotFoundException;

/**
 * This action fetches data of a given subject
 */
class FindAction extends Action {

    /**
     * Find action type
     * 
     * @static
     * @var string
     */
    static $type = 'find';

    /**
     * Data preparation
     */
    function prepareData() 
    {
        if(isset($this->payload['id'])) {
            // Return a single document
            $document = $this->collection->pick((int)$this->payload['id']);

            if(!$document) {
                throw new NotFoundException('Document not found');
            }

            return $document;
        }

        $query = $this->collection->find();

        if(isset($this->payload['filter'])) {
            $query->filter($this->payload['filter']);
        }

        if(isset($this->payload['format'])) {
            $query->format($this->payload['format']);
        }

        return $query;
    }
}
