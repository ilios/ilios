<?php

namespace Ilios\CoreBundle\Service;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;

/**
 * Fetch things from the interwebs - exposed as a service for
 * reusability, error handling, and configuration options
 *
 * @package Ilios\CoreBundle\Service
 *
 */
class Fetch
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function __construct(HttpClient $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function get(string $url) : string
    {
        $request = $this->requestFactory->createRequest('GET', $url);
        $response = $this->client->sendRequest($request);
        $fileContents = $response->getBody()->getContents();

        if (empty($fileContents)) {
            throw new \Exception('Failed to load ' . $url);
        }

        return $fileContents;
    }
}
