<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SwaggerYamlDocsControllerTest extends WebTestCase
{
    /**
     * Ensure that the page will load correctly
     */
    public function testLoad()
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc/swagger.yml');
        $response = $client->getResponse();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            substr($response->getContent(), 0, 400)
        );

        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/x-yaml'
            ),
            $response->headers
        );
        
        $this->assertGreaterThan(1000, strlen($response->getContent()), 'API Endpoints loaded');
    }
}
