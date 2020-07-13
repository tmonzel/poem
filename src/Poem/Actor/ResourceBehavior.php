<?php

namespace Poem\Actor;

use Poem\Actor\Actions\CreateAction;
use Poem\Actor\Actions\DestroyAction;
use Poem\Actor\Actions\FindAction;
use Poem\Actor\Actions\PickAction;
use Poem\Actor\Actions\UpdateAction;

class ResourceBehavior extends Behavior {
    function initialize() {
        $this->registerAction(FindAction::class);
        $this->registerAction(DestroyAction::class);
        $this->registerAction(UpdateAction::class);
        $this->registerAction(PickAction::class);
        $this->registerAction(CreateAction::class);
    }
}