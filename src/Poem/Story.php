<?php

namespace Poem;

use Exception;
use Poem\Actor\Exceptions\ActionException;
use Poem\Actor\Exceptions\BadRequestException;
use Poem\Actor\Exceptions\NotFoundException;
use Poem\Actor\Worker as ActorWorker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Story 
{
    /**
     * Applied director
     * 
     * @var Director
     */
    protected $director;

    /**
     * Action endpoint
     * 
     * @var string
     */
    protected $endpoint;

    /**
     * Create a new story.
     * 
     * @param Director $director
     * @param string $endpoint
     */
    function __construct(Director $director, string $endpoint = '/api') 
    {
        $this->director =  $director;
        $this->endpoint = $endpoint;
    }

    /**
     * Resolve request and send json response to output.
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

        $this->director->eachWorkerWithInterface(
            RequestHandler::class, 
            function(RequestHandler $worker) use($request) {
                $worker->handleRequest($request);
            }
        );

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
    protected function parseQuery(array $data) 
    {
        if(isset($data[0])) {
            // Parse multiple actions
            return array_map(function($d) {
                return $this->parseQuery($d);
            }, $data);
        }
        
        if(!isset($data['type'])) {
            throw new BadRequestException('No type defined');
        }

        if(!isset($data['action'])) {
            throw new BadRequestException('No action defined');
        }
        
        $actors = $this->director->accessWorker(
            ActorWorker::Accessor
        );

        try {
            /** @var Actor $actor */
            $actor = $actors->access($data['type']);
        } catch(Exception $e) {
            throw new NotFoundException('Actor `' . $data['type'] . '` not registered');
        }

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
}