<?php

namespace User;

class Model extends \Poem\Auth\User 
{
    /**
     * User validations
     * 
     * @return array
     */
    function initialize(): void
    {
        $this->addValidation('name', 'required');
        $this->addValidation('password', 'required');
    }
}
