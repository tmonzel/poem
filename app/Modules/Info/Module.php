<?php

namespace Modules\Info;

use Poem\Actor;

class Module extends \Poem\Module
{
    /**
     * Preparing the actor instance for this module
     * 
     * @param Actor $actor
     * @return void
     */
    static function prepareActor(Actor $actor): void 
    {
        $actor->registerAction('stats', function($payload) {
            return ['test' => 'foo'];
        });
        
        $actor->guardActions();
    }
}
