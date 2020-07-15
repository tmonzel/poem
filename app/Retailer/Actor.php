<?php

namespace Retailer  {
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\AuthGuard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            AuthGuard::class => [
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