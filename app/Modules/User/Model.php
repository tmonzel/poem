<?php

namespace Modules\User;

class Model extends \Poem\Model 
{
    /**
     * Initialize user model
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->addValidation('name', 'required');
        $this->addValidation('password', 'required');

        // Add mutator for password attribute
        $this->mutateAttribute('password', function($value) {
            return password_hash($value, PASSWORD_ARGON2I);
        });
    }
}
