<?php

namespace Poem\Actor\Exceptions;

class BadRequestException extends ActionException {
    function __construct($message, $errors = []) {
        parent::__construct($message, 400, $errors);
    }
}