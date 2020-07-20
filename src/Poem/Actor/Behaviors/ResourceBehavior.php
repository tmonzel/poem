<?php

namespace Poem\Actor\Behaviors;

use Poem\Actor;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\UpdateAction;
use Poem\Actor\Behavior;

class ResourceBehavior extends Behavior {
    function initialize(Actor $actor, string $actionType, array $payload = []) {
        $actor->registerAction(FindAction::class);
        $actor->registerAction(DestroyAction::class);
        $actor->registerAction(UpdateAction::class);
        $actor->registerAction(CreateAction::class);
    }
}