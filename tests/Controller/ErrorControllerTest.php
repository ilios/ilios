<?php

namespace App\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;
use Faker\Factory as FakerFactory;

class ErrorControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    public function setUp()
    {
        $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData',
        ]);
    }

    public function testIndex()
    {
        $faker = FakerFactory::create();

        $client = static::createClient();
        $data = [
            'mainMessage' => $faker->text(100),
            'stack' => $faker->text(1000)
        ];
        $this->makeJsonRequest(
            $client,
            'POST',
            '/errors',
            json_encode(['data' => json_encode($data)]),
            $this->getAuthenticatedUserToken()
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());
    }
}
