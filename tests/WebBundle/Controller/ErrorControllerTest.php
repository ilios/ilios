<?php

namespace Tests\WebBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Faker\Factory as FakerFactory;

class ErrorControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
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
