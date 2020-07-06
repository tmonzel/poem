<?php

namespace Poem\Actor\Exceptions;

class UnauthorizedException extends ActionException {
    function __construct($message, $errors = []) {
        parent::__construct($message, 401, $errors);
    }
}