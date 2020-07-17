<?php

namespace Poem;

use Poem\Actor\ActionQuery;
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
     * Construct a new story
     * 
     * @param string $endpoint
     */
    function __construct(string $endpoint = '/api') 
    {
        $this->endpoint = $endpoint;
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
        $data = [];

        try {
            $data = $this->resolveData($request);
        } catch(ActionException $e) {
            $response->setStatusCode($e->getCode());
            $errors = $e->getErrors();

            if(count($errors) === 0) {
                $errors = [
                    ['status' => $e->getCode(), 'title' => $e->getMessage()]
                ];
            }

            $data = compact('errors');
        }

        $response->setData($data);
        
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

        $data = $this->parseQueryData($request);

        if(!$data) {
            throw new BadRequestException('Invalid query. Please provide valid json format');
        }

        if(!isset($data['type'])) {
            throw new BadRequestException('No type defined');
        }

        if(!isset($this->actors[$data['type']])) {
            throw new NotFoundException('Type ' . $data['type'] . ' not available');
        }

        if(!isset($data['action'])) {
            throw new BadRequestException('No action defined');
        }

        /** @var Actor $actor */
        $actor = new $this->actors[$data['type']];

        $query = new ActionQuery(
            $data['action'], 
            isset($data['payload']) ? $data['payload'] : [], 
            $request->headers->all()
        );

        $headers = $request->headers->all();

        if(isset($headers['authorization']) && isset($headers['authorization'][0])) {
            $token = $headers['authorization'][0];

            // Check header for token
            // Find user for action query
            $query->auth = new Auth($token);
        }

        return $actor->invokeQuery($query);
    }

    /**
     * 
     */
    private function parseQueryData(Request $request)
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