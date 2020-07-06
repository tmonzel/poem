<?php

namespace Poem;

use Poem\Actor\ActionDispatcher;
use Slim\App;

class Actor {
    static $Behaviors = [];
    static $baseRoute;

    static function getNamespace(): string {
        $className = get_called_class();
        return substr($className, 0, strrpos($className, '\\'));
    }

    static function getSubjectClass(): string {
        return static::getNamespace() . '\\Model';
    }

    public static function introduce(App $app) {
        $actor = new static();
        $actor->act($app);
    }

    function prepareActions(ActionDispatcher $actions) {

    }

    function buildBehaviors(): array {
        $behaviors = [];

        foreach(static::$Behaviors as $k => $behaviorClass) {
            if(is_numeric($k)) {
                $behaviors[] = new $behaviorClass();
            }                
        }

        return $behaviors;
    }

    public function act(App $app) {
        $behaviors = $this->buildBehaviors();
        $subjectClass = static::getSubjectClass();
        $baseRoute =  static::$baseRoute ?? $subjectClass::type();
        $actions = new ActionDispatcher($baseRoute, $subjectClass);
        
        foreach($behaviors as $behavior) {
            $behavior->prepareActions($actions);
        }
        
        $this->prepareActions($actions);
        $actions->dispatch($app);
    }
}