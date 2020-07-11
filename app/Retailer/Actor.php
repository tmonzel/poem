<?php

namespace Retailer  {
    use Poem\Actor\Actions\FindAction;
    use Poem\Actor\Actions\PickAction;
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\Guard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            Guard::class => [
                'allowActions' => [
                    FindAction::class,
                    PickAction::class
                ]
            ]
        ];

        function create($action) {
            // modify create action
        }

        function find($action) {
            // modify find action
        }
    }
}