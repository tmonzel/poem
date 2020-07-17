<?php

namespace Retailer  {

    use Poem\Actor\Behaviors\{
        ResourceBehavior, 
        GuardBehavior
    };

    class Actor extends \Poem\Actor {
        const Behaviors = [
            ResourceBehavior::class,
            GuardBehavior::class => [
                'permit' => [
                    '*' => ['admin'],
                    'find' => ['admin']
                ],

                'except' => [
                    'find', 
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