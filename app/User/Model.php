<?php

namespace User;

class Model extends \Poem\Auth\User 
{
    /**
     * User validations
     * 
     * @return array
     */
    function validations(): array
    {
        return [
            'name' => 'required',
            'password' => 'required'
        ];
    }
}
