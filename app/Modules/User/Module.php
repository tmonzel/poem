<?php

namespace Modules\User;

use Actor;
use Poem\Auth\Actions\LoginAction;
use Poem\Auth\Actions\MeAction;
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
        // Binding actions
        $actor->bind(Actor::RESOURCE_ACTIONS);
        $actor->bind(LoginAction::class);
        $actor->bind(MeAction::class);

        // Guarding actions with exceptions
        $actor->guardActions([
            'find'
        ]);
    }
}
