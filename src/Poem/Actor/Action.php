<?php

namespace Poem\Actor;

use JsonSerializable;
use Poem\Actor\Exceptions\ActionException;
use Poem\Actor\Exceptions\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class Action {
    static $method;
    static $route;
    
    protected $subjectClass;

    static function getMethod() {
        return static::$method;
    }

    static function getRoute() {
        return static::$route;
    }

    function __invoke(Request $request, Response $response, array $args) {
        try {
            $data = $this->prepareData($request);
        }
        catch(NotFoundException $e) {
            $response = $response->withStatus(404);
            $data = [
                'errors' => [
                    [
                        'status' => 404,
                        'code' => 'not-found',
                        'title' => $e->getMessage(),
                    ]
                ]
            ];
        }
        catch(ActionException $e) {
            $response = $response->withStatus($e->getCode());
            $data = [ 'errors' => $e->getErrors() ];
        }


        if($data instanceof JsonSerializable || is_array($data)) {
            try {
                $data = json_encode($data, 
                JSON_HEX_TAG | 
                JSON_HEX_APOS | 
                JSON_HEX_AMP | 
                JSON_HEX_QUOT | 
                JSON_THROW_ON_ERROR |
                JSON_INVALID_UTF8_IGNORE);

            } catch (\Exception $e) {
                if ('Exception' === \get_class($e) && 0 === strpos($e->getMessage(), 'Failed calling ')) {
                    throw $e->getPrevious() ?: $e;
                }
                throw $e;
            }

            $response = $response->withHeader('Content-Type', 'application/json');
            
        }

        $response->getBody()->write($data);
        
        return $response;
    }

    function setSubject($subjectClass) {
        $this->subjectClass = $subjectClass;
    }

    function prepareData(Request $request) {
        
    }
}