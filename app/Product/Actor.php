<?php

namespace Product  {
    use Poem\Actor\Actions\FindAction;
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\Guard;

    class Actor extends \Poem\Actor {
        static $Behaviors = [
            ResourceBehavior::class,
            Guard::class => [
                'role' => 'admin',
                'allowActions' => [FindAction::class]
            ]
        ];
    }
}