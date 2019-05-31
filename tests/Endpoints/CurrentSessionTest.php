<?php

namespace App\Tests\Endpoints;

use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

/**
 * AamcMethod API endpoint Test.
 * @group api_3
 */
class CurrentSessionTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;


    public function setUp()
    {
        $fixtures = [
            'App\Tests\Fixture\LoadAuthenticationData',
            'App\Tests\Fixture\LoadUserData',
        ];
        $this->fixtures = $this->loadFixtures($fixtures)->getReferenceRepository();
    }

    public function tearDown() : void
    {
        unset($this->fixtures);
    }

    public function testGetGetCurrentSession()
    {
        $client = static::createClient();
        $url = $this->getUrl(
            'ilios_api_currentsession',
            ['version' => 'v1']
        );
        $this->makeJsonRequest($client, 'GET', $url, null, $this->getAuthenticatedUserToken());

        $response = $client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(2, $data['userId']);
    }
}
