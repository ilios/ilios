<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

class ApiTestCase extends WebTestCase
{

    /**
     * Create a JSON request
     * 
     * @param string $method
     * @param string $url
     * @param string $content
     * 
     * @return Symfony\Bundle\FrameworkBundle\Client
     */
    public function createJsonRequest($method, $url, $content = null)
    {
        $client = static::createClient();

        $client->request(
            $method,
            $url,
            array(),
            array(),
            array(
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $content
        );

        return $client;
    }

    /**
     * Check if the response is valid
     * tests the status code, headers, and the content
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param integer $statusCode
     * @param boolean $checkValidJson
     */
    protected function assertJsonResponse(
        \Symfony\Component\HttpFoundation\Response $response,
        $statusCode,
        $checkValidJson = true
    ) {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            ),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(
                ($decode != null && $decode != false),
                'Invalid JSON: [' . $response->getContent() . ']'
            );
        }
    }
}
