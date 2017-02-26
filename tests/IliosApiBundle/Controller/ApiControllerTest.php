<?php

namespace Tests\CoreBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\CoreBundle\Traits\JsonControllerTest;

class ApiControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadAuthenticationData'
        ]);
    }

    public function testNoEndpoint()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/v1/nothing',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertEquals(
            404,
            $response->getStatusCode(),
            substr($response->getContent(), 0, 400)
        );
    }

    public function testNoVersion()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/nothing',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertEquals(
            404,
            $response->getStatusCode(),
            substr($response->getContent(), 0, 400)
        );
    }

    public function testBadVersion()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/1/courses',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertEquals(
            404,
            $response->getStatusCode(),
            substr($response->getContent(), 0, 400)
        );
    }
}
