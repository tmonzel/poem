<?php

namespace Poem;

use Poem\Actor\ActionQuery;
use Poem\Actor\ActionResolver;
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
            $data = $this->resolveData($request);
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
     * 
     * 
     * @param Request $request
     * @return mixed
     */
    private function resolveData(Request $request)
    {
        if($request->getRequestUri() !== $this->endpoint) {
            throw new NotFoundException('Endpoint not found');
        }

        if(array_search($request->getMethod(), ['GET', 'POST']) === false) {
            throw new BadRequestException('Invalid method used');
        }

        $headers = $request->headers->all();

        if(isset($headers['authorization']) && isset($headers['authorization'][0])) {
            $token = $headers['authorization'][0];
            $this->auth->setToken($token);
        }

        $data = $this->encodeRequestBody($request);

        if(!$data) {
            throw new BadRequestException('Invalid query. Please provide valid json format');
        }

        return $this->prepareQueryData($data);
    }

    function prepareQueryData(array $data) 
    {
        if(isset($data[0])) {
            // Multiple actions
            return array_map(function($d) {
                return $this->prepareQueryData($d);
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
     * 
     */
    private function encodeRequestBody(Request $request): array
    {
        $rawBody = $request->getContent();
        return $rawBody ? json_decode($rawBody, true) : [];
    }

    /**
     * Create new story
     * 
     * @return Story
     */
    static function new(...$args): self 
    {
        return new static(...$args);
    }
}