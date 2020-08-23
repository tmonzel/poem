<?php

namespace Modules\User;

use Poem\Model\FindQuery;

class Model extends \Poem\Model 
{
    /**
     * Initialize user model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->validateAttribute('name', 'required');
        $this->validateAttribute('password', 'required');

        // Add mutator for password attribute
        $this->mutateAttribute('password', function($value) {
            return password_hash($value, PASSWORD_ARGON2I);
        });
    }

    function findByRole(string $role): FindQuery
    {
        return $this->find()->filter(['role' => $role]);
    }
}
