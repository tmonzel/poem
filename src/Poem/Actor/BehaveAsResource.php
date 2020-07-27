<?php

namespace Poem\Actor;

use Poem\Actor;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\UpdateAction;
use Poem\Behavior;

/**
 * Actor behaves as a Resource
 * Adds default CRUD actions
 */
class BehaveAsResource extends Behavior {
    function initialize()
    {
        $this->shouldActOn(Actor::PREPARE_ACTION_EVENT, [$this, 'prepareAction']);
    }

    function prepareAction(Actor $actor, array $payload = []) {
        $actor->registerAction(FindAction::class);
        $actor->registerAction(DestroyAction::class);
        $actor->registerAction(UpdateAction::class);
        $actor->registerAction(CreateAction::class);
    }
}