<?php

namespace Modules\User;

use Poem\Auth\Actions\LoginAction;
use Poem\Auth\Actions\MeAction;

class Actor extends \Poem\Actor
{
    /**
     * Initialize user actor
     * 
     * @return void
     */
    function initialize(): void
    {
        // Binding actions
        $this->bind(static::RESOURCE_ACTIONS);
        $this->bind(LoginAction::class);
        $this->bind(MeAction::class);

        // Guarding actions with exceptions
        $this->guardActions([
            'find', 'login'
        ]);
    }

    protected function infoAction() {
        return ['test' => 'bla'];
    }
}
