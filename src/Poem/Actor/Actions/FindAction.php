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
        // Access the related model
        $model = $this->actor->getModel();
        
        if($this->payload->has('id')) {
            // Return a single document
            $document = $model->pick($this->payload->id);

            if(!$document) {
                throw new NotFoundException('Document not found');
            }

            return $document;
        }

        $query = $model->find();

        if($this->payload->has('ids')) {
            $query->filter(['id' => $this->payload->ids]);
        } else if($this->payload->has('filter')) {
            $query->filter($this->payload->filter);
        }

        if($this->payload->has('format')) {
            $query->format($this->payload->format);
        }

        if($this->payload->has('include')) {
            $query->include($this->payload->include);
        }

        return $query;
    }
}
