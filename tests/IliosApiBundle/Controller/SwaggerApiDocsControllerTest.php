<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SwaggerApiDocsControllerTest extends WebTestCase
{
    /**
     * Ensure that the page will load correctly
     */
    public function testLoad()
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc/');
        $response = $client->getResponse();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            substr($response->getContent(), 0, 400)
        );

        $this->assertGreaterThan(100, strlen($response->getContent()), 'API Endpoints loaded');
    }
}
