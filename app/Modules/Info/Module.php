<?php

namespace Modules\Info;

use Actor;
use Poem\Module\Actable;

class Module extends \Module
{
    use Actable;

    function withActor(Actor $actor) 
    {
        $actor->registerAction('stats', function($payload) {
            return ['test' => 'foo'];
        });
    }
}
