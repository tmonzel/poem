<?php

namespace Modules\User;

use Actor;
use Poem\Module\Actable;
use Poem\Module\Storable;

class Module extends \Module 
{
    use Actable, Storable;

    static function getType(): string
    {
        return 'users';
    }

    function withActor(Actor $actor)
    {
        $actor->bind(Actor::RESOURCE_ACTIONS);
        $actor->guardActions(['create']);
    }
}
