<?php

namespace Poem;

use Slim\App;
use Slim\Factory\AppFactory;

class Story {
    protected $app;
    protected $actors = [];

    public function __construct(App $app) {
        $this->app = $app;
    }

    function about($actorClass) {
        $this->actors[] = $actorClass;
    }

    function tell() {
        foreach($this->actors as $actorClass) {
            $actorClass::introduce($this->app);
        }

        $this->app->run();
    }

    static function create(): self {
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(true, true, true);
        return new static($app);
    }
}