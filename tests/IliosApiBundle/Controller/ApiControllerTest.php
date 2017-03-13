<?php

namespace Tests\CoreBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
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
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
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
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
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
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
