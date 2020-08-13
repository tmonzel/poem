<?php

namespace Modules\Product;
use Poem\Actor\Actions\FindAction;
use Poem\Module\Actable;
use Poem\Module\Storable;

class Module extends \Poem\Module 
{
    use Actable, Storable;

    static function getType(): string
    {
        return 'products';
    }

    function withActor(Actor $actor)
    {
        $actor->bind(FindAction::class);
    }
}
