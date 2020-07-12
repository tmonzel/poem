<?php

namespace Retailer  {
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\Guard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            Guard::class => [
                'except' => [
                    'find', 
                    'pick', 
                    'create'
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