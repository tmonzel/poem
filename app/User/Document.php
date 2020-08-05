<?php

namespace User;

class Document extends \Poem\Model\Document 
{        
    /**
     * Hide the following attributes from serialization
     * 
     * @var array
     */
    const Hide = [
        'password'
    ];
}
