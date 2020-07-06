<?php

namespace Poem\Actor;

use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\PickAction;
use Poem\Actor\Actions\UpdateAction;

class ResourceBehavior implements Behavior {
    function prepareActions(ActionDispatcher $actions) {
        $actions->add(FindAction::class);
        $actions->add(DestroyAction::class);
        $actions->add(UpdateAction::class);
        $actions->add(PickAction::class);
        $actions->add(CreateAction::class);
    }
}