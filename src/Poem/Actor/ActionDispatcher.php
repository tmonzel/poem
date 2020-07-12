<?php

namespace Poem\Actor;

use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;

class ActionDispatcher {
    protected $actions = [];
    protected $subjectClass;
    protected $listeners = [];

    function __construct($subjectClass = null) {
        $this->subjectClass = $subjectClass;
    }

    function add(string $actionClass, callable $initializer = null) {
        $this->actions[$actionClass::getType()] = compact('actionClass', 'initializer');
    }

    function addListener($name, callable $hook) {
        if(!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        $this->listeners[$name][] = $hook;
    }

    function dispatch(array $query) 
    {
        if(!isset($query['type'])) {
            throw new BadRequestException('No action type defined');
        }
        
        if(!isset($this->actions[$query['type']])) {
            throw new NotFoundException($query['type'] . " is not registered on " . $this->subjectClass::Type);
        }

        extract($this->actions[$query['type']]);

        $action = new $actionClass;
        $action->setSubject($this->subjectClass);

        if(isset($query['payload'])) {
            $action->setPayload($query['payload']);
        }

        if(isset($this->listeners['before'])) {
            foreach($this->listeners['before'] as $hook) {
                call_user_func($hook, $action);
            }
        }

        if(isset($initializer)) {
            $initializer($action);
        }
        
        return $action->dispatch();
    }
}
