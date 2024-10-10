<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use SplFileObject;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fetch things from the interwebs - exposed as a service for
 * reusability, error handling, and configuration options
 *
 * @package App\Service
 *
 */
class Fetch
{
    public function __construct(protected HttpClientInterface $client)
    {
    }

    /**
     * Get a file
     *
     * If passed a $file it will use that to check if the response would be a 304 Not Modified
     * and if so just return the contents of $file to save bandwidth downloading it again
     *
     * @param SplFileObject|null $file
     * @throws Exception
     */
    public function get(string $url, ?SplFileObject $file = null): string
    {
        $headers = [];
        if ($file) {
            $lastModifiedTime = $file->getMTime();
            $lastModified = new DateTime();
            $lastModified->setTimestamp($lastModifiedTime);
            $headers['if-modified-since'] = $lastModified->format('D, d M Y H:i:s T');
        }

        $response = $this->client->request('GET', $url, [
            'headers' => $headers,
        ]);

        if ($file && $response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
            $fileContents = $file->fread($file->getSize());
        } else {
            $fileContents = $response->getContent();
        }

        if (empty($fileContents)) {
            throw new Exception('Failed to load ' . $url);
        }

        return $fileContents;
    }
}
