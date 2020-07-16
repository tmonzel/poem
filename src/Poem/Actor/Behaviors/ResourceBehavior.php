<?php

namespace Poem\Actor\Behaviors;

use Poem\Actor\ActionQuery;
use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\UpdateAction;
use Poem\Actor\Behavior;

class ResourceBehavior extends Behavior {
    function initialize(ActionQuery $query) {
        $this->registerAction(FindAction::class);
        $this->registerAction(DestroyAction::class);
        $this->registerAction(UpdateAction::class);
        $this->registerAction(CreateAction::class);
    }
}