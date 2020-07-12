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
    function about(string $actorClass) 
    {
        $this->actors[$actorClass::getType()] = $actorClass;
    }

    function tell(Request $request = null) 
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

        $query = $this->parseQuery($request);

        if(!isset($query['subject'])) {
            throw new BadRequestException('No subject defined');
        }

        if(!isset($this->actors[$query['subject']])) {
            throw new NotFoundException('Subject not registered');
        }

        $actor = new $this->actors[$query['subject']];
        return $actor->act($query);
    }

    /**
     * 
     */
    private function parseQuery(Request $request)
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