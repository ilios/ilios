<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

class ApiControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    public function setUp()
    {
        $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    public function testNoEndpoint()
    {
        $client = static::createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/v1/nothing',
            null,
            $this->getTokenForUser($client, 1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testNoVersion()
    {
        $client = static::createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/nothing',
            null,
            $this->getTokenForUser($client, 1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testBadVersion()
    {
        $client = static::createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/1/courses',
            null,
            $this->getTokenForUser($client, 1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
