<?php

namespace Poem\Actor\Actions;

use Poem\Actor\Action;

class FindAction extends Action {
    static $type = 'find';

    function prepareData() 
    {
        return $this->subject::find();
    }
}
