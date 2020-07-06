<?php

namespace Poem\Actor;

use Slim\App;

class ActionDispatcher {
    private $baseRoute;
    private $actions = [];
    private $subjectClass;

    function __construct($baseRoute = null, $subjectClass = null) {
        $this->baseRoute = $baseRoute;
        $this->subjectClass = $subjectClass;
    }

    function add(string $actionClass, callable $configurator = null) {
        $this->actions[$actionClass] = $configurator;
    }

    function dispatch(App $app) {
        $subjectClass = $this->subjectClass;

        foreach($this->actions as $actionClass => $configurator) {
            $method = $actionClass::getMethod();
            $route = $actionClass::getRoute();

            $app->{$method}('/' . $this->baseRoute . $route, 
                function($request, $response, $args) use($actionClass, $configurator, $subjectClass) {
                    /** @var Action $action */
                    $action = new $actionClass();

                    if($subjectClass) {
                        $action->setSubject($subjectClass);
                    }

                    if(is_callable($configurator)) {
                        $configurator($action);
                    }

                    if(!$action->canActivate()) {
                        // Return 401 Unauthorized
                    }

                    return call_user_func($action, $request, $response, $args);
                }
            );
        }
    }
}
