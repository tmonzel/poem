<?php

namespace Retailer  {

    use Poem\Actor\BehaveAsResource;
    use Poem\Auth\BehaveAsGuard;

    class Actor extends \Poem\Actor {
        const Behaviors = [
            BehaveAsResource::class,
            BehaveAsGuard::class => [
                'permit' => [
                    '*' => ['admin'],
                    'find' => ['admin']
                ],

                'except' => [
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