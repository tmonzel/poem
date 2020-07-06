<?php

namespace Poem\Actor\Exceptions;

use Exception;

class ActionException extends Exception {
    private $errors;

    function __construct($message, $code, $errors = []) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    function getErrors(): array {
        return $this->errors;
    }
}