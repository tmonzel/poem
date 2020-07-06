<?php

namespace Poem\Actor;

use Poem\Actor\Exceptions\ActionException;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

class ActionDispatcher {
    private $baseRoute;
    private $actions = [];
    public $subjectClass;
    private $listeners = [];

    function __construct($baseRoute = null, $subjectClass = null) {
        $this->baseRoute = $baseRoute;
        $this->subjectClass = $subjectClass;
    }

    function add(string $actionClass, callable $configurator = null) {
        $this->actions[$actionClass] = $configurator;
    }

    function addListener($name, callable $hook) {
        if(!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        $this->listeners[$name][] = $hook;
    }

    function dispatch(App $app) {
        $dispatcher = $this;

        foreach($this->actions as $actionClass => $configurator) {
            $method = $actionClass::getMethod();
            $route = $actionClass::getRoute();

            $app->{$method}('/' . $this->baseRoute . $route, 
                function($request, ResponseInterface $response, $args) use($actionClass, $configurator, $dispatcher) {
                    /** @var Action $action */
                    $action = new $actionClass();

                    if($dispatcher->subjectClass) {
                        $action->setSubject($dispatcher->subjectClass);
                    }

                    if(is_callable($configurator)) {
                        $configurator($action);
                    }

                    try {

                        if(isset($dispatcher->listeners['before'])) {
                            foreach($dispatcher->listeners['before'] as $callback) {
                                $callback($action, $request);
                            }
                        }

                        // Before action hooks
                        /** @var ResponseInterface $response */
                        $response = call_user_func($action, $request, $response, $args);

                        // After action hooks

                    } catch(ActionException $e) {
                        return $response->withStatus($e->getCode());
                    }

                    return $response;
                }
            );
        }
    }
}
