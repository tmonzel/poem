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
     * Prepare data for execution
     * 
     * @return mixed
     */
    function prepareData() 
    {
        if($this->payload->has('id')) {
            // Return a single document
            $document = $this->collection->pick((int)$this->payload->id);

            if(!$document) {
                throw new NotFoundException('Document not found');
            }

            return $document;
        }

        $query = $this->collection->find();

        if($this->payload->has('filter')) {
            $query->filter($this->payload->filter);
        }

        if($this->payload->has('format')) {
            $query->format($this->payload->format);
        }

        return $query;
    }
}
