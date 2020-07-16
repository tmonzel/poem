<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;

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
            $document = $this->subject::pick((int)$this->payload['id']);
            return $document->toData($this->payload);
        }
        
        $data = [];
        $conditions = isset($this->payload['conditions']) ? $this->payload['conditions'] : [];
        $documents = $this->subject::find($conditions);

        foreach($documents as $d) {
            $data[] = $d->toData($this->payload);
        }

        return $data;
    }
}
