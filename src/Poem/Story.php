<?php

namespace Poem;

use Slim\App;
use Slim\Factory\AppFactory;

class Story {
    protected $app;

    public function __construct(App $app) {
        $this->app = $app;
    }

    function about($actorClass) {
        $actorClass::introduce($this->app);
    }

    function tell() {
        $this->app->run();
    }

    static function create(): self {
        $app = AppFactory::create();
        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(true, true, true);
        return new static($app);
    }
}