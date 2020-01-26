<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadAuthenticationData;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;
use Faker\Factory as FakerFactory;

class ErrorControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([
            LoadAuthenticationData::class,
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
            $this->getAuthenticatedUserToken($client)
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());
    }
}
