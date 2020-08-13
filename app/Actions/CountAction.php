<?php

namespace Actions;

use Poem\Actor\Action;

class CountAction extends Action {

    static $type = 'count';

    function prepareData()
    {
        $model = $this->actor->getModel();
        
        
        return [$model->getName() . "Count"  => $model->count()];
    }
}