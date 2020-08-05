<?php

namespace Poem\Model;

trait Validateable {
    abstract function validations(): array;

    private $validationErrors = [];

    function valid(): bool {
        $validations = $this->validations();
        $attributes = $this->attributes;
        $this->validationErrors = [];

        foreach($validations as $name => $config) {
            if(!is_array($config)) {
                $config = [$config];    
            }

            foreach($config as $test) {
                switch($test) {
                    case 'required':
                        if(!isset($attributes[$name]) || !$attributes[$name]) {
                            $this->validationErrors[$name] = [
                                'status' => 400,
                                'title' => $name . " is required"
                            ];
                        }
                }
            }
        }

        return count($this->validationErrors) === 0;
    }

    function validationErrors() {
        return $this->validationErrors;
    }
}