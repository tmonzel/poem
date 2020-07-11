<?php

namespace Poem\Actor\Exceptions;

class NotFoundException extends ActionException {
    function __construct($message, $errors = []) {
        parent::__construct($message, 404, $errors);
    }
}