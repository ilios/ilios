<?php

namespace Ilios\CoreBundle\Service;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use \DateTime;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Get a file
     *
     * If passed a $file it will use that to check if the response would be a 304 Not Modified
     * and if so just return the contents of $file to save bandwidth downloading it again
     *
     * @param string $url
     * @param \SplFileObject|null $file
     * @return string
     * @throws \Exception
     */
    public function get(string $url, \SplFileObject $file = null) : string
    {
        $request = $this->requestFactory->createRequest('GET', $url);

        if ($file) {
            $lastModifiedTime = $file->getMTime();
            $lastModified = new DateTime();
            $lastModified->setTimestamp($lastModifiedTime);
            $request = $request->withHeader('if-modified-since', $lastModified->format('D, d M Y H:i:s T'));
        }

        $response = $this->client->sendRequest($request);
        if ($file && $response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
            $fileContents = $file->fread($file->getSize());
        } else {
            $fileContents = $response->getBody()->getContents();
        }

        if (empty($fileContents)) {
            throw new \Exception('Failed to load ' . $url);
        }

        return $fileContents;
    }
}
