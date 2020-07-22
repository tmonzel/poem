<?php

namespace Poem;

use Poem\Actor\Exceptions\ActionException;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Story 
{
    /**
     * Registered actor classes
     * 
     * @var array
     */
    protected $actors = [];

    /**
     * Action endpoint
     * 
     * @var string
     */
    protected $endpoint;

    /**
     * 
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new story
     * 
     * @param string $endpoint
     * @param Auth $auth
     */
    function __construct(string $endpoint = '/api', Auth $auth = null) 
    {
        $this->endpoint = $endpoint;
        $this->auth = $auth ?? new Auth();
    }

    /**
     * Return all registered actors
     * 
     * @return array
     */
    function getActors(): array {
        return $this->actors;
    }

    /**
     * Return authenticator
     * 
     * @return Auth
     */
    function getAuth(): Auth {
        return $this->auth;
    }

    /**
     * Register an actor
     * 
     * @param string $actorClass
     */
    function about(string $actorClass): void
    {
        $this->actors[$actorClass::getType()] = $actorClass;
    }

    /**
     * Resolve request and send json response to output
     * 
     * @param Request $request
     */
    function tell(Request $request = null): void
    {
        $request = $request ?? Request::createFromGlobals();
        $response = new JsonResponse();

        try {
            $data = $this->resolveQuery($request);
            $response->setData($data);
        } catch(ActionException $e) {
            $response->setStatusCode($e->getCode());
            $errors = $e->getErrors();

            if(count($errors) === 0) {
                $errors = [
                    ['status' => $e->getCode(), 'title' => $e->getMessage()]
                ];
            }

            $response->setData(compact('errors'));
        }

        // Deliver to output
        $response->send();
    }

    /**
     * Validate and resolve the query
     * Initialize auth helper
     * 
     * @param Request $request
     * @return mixed
     */
    function resolveQuery(Request $request)
    {
        if($request->getRequestUri() !== $this->endpoint) {
            throw new NotFoundException('Endpoint not found');
        }

        if(array_search($request->getMethod(), ['GET', 'POST']) === false) {
            throw new BadRequestException('Invalid method used');
        }

        $this->auth->initialize($request);

        $data = $this->parseQueryData($request);

        if(!$data) {
            throw new BadRequestException('Invalid query data. Please provide valid json format');
        }

        return $this->parseQuery($data);
    }

    /**
     * Parse the query data
     * 
     * @param array $data
     * @return mixed
     */
    function parseQuery(array $data) 
    {
        if(isset($data[0])) {
            // Multiple actions
            return array_map(function($d) {
                return $this->parseQuery($d);
            }, $data);
        }

        $actors = $this->getActors();
        
        if(!isset($data['type'])) {
            throw new BadRequestException('No type defined');
        }

        if(!isset($actors[$data['type']])) {
            throw new NotFoundException('Type ' . $data['type'] . ' not available');
        }

        if(!isset($data['action'])) {
            throw new BadRequestException('No action defined');
        }

        /** @var Actor $actor */
        $actor = new $actors[$data['type']]($this);

        return $actor->prepareAction(
            $data['action'], 
            isset($data['payload']) ? $data['payload'] : []
        );
    }

    /**
     * Return the json decoded request body
     * 
     * @param Request $request
     * @return mixed
     */
    private function parseQueryData(Request $request)
    {
        $rawBody = $request->getContent();
        return $rawBody ? json_decode($rawBody, true) : [];
    }

    /**
     * Create a new story helper
     * 
     * @return Story
     */
    static function new(...$args): self 
    {
        return new static(...$args);
    }
}