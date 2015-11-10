<?php

namespace Ilios\WebBundle\Tests\Controller;

use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\Factory as FakerFactory;
use FOS\RestBundle\Util\Codes;

class ErrorControllerTest extends WebTestCase
{
    use JsonControllerTest;

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
            json_encode(['data' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());

    }
}
