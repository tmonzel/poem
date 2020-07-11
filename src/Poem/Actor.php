<?php

namespace Poem;

use Poem\Actor\ActionDispatcher;
use Slim\App;

class Actor {
    static $baseRoute;

    static function getNamespace(): string {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getSubjectClass(): string {
        return static::getNamespace() . '\\Model';
    }

    static function introduce(App $app) {
        $actor = new static();
        $actor->act($app);
    }

    function prepareActions(ActionDispatcher $actions) {

    }

    function buildBehaviors(): array {
        $behaviors = [];
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Behaviors')) {
            return $behaviors;
        }

        foreach($calledClass::Behaviors as $k => $behaviorClass) {
            if(is_numeric($k)) {
                $behaviors[] = new $behaviorClass();
            } else {
                $behaviors[] = new $k($behaviorClass);
            }
        }

        return $behaviors;
    }

    function act(App $app) {
        $behaviors = $this->buildBehaviors();
        $subjectClass = static::getSubjectClass();
        $baseRoute =  static::$baseRoute ?? $subjectClass::Type;
        $actions = new ActionDispatcher($baseRoute, $subjectClass);
        
        foreach($behaviors as $behavior) {
            $behavior->prepareActions($actions);
        }
        
        $this->prepareActions($actions);
        $actions->dispatch($app);
    }
}