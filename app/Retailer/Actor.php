<?php

namespace Retailer  {
    use Poem\Actor\ResourceBehavior;
    use Poem\Auth\JwtGuard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            JwtGuard::class => [
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