<?php

namespace Modules\User;

use Poem\Actor;
use Poem\Module\Actable;
use Poem\Module\Storable;

class Module extends \Poem\Module 
{
    use Actable, Storable;

    static function getType(): string
    {
        return 'users';
    }

    function withActor(Actor $actor)
    {
        $actor->bind(Actor::RESOURCE_ACTIONS);

        // TODO: Add guard behaviors
    }
}
