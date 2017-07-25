<?php

namespace Tests\IliosApiBundle\Endpoints;

use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * AamcMethod API endpoint Test.
 * @group api_3
 */
class CurrentSessionTest extends WebTestCase
{
    use JsonControllerTest;


    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;


    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->container = $this->client->getContainer();

        $fixtures = [
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
        ];
        $this->fixtures = $this->loadFixtures($fixtures)->getReferenceRepository();
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->container);
        unset($this->fixtures);
    }

    public function testGetGetCurrentSession()
    {
        $url = $this->getUrl(
            'ilios_api_currentsession',
            ['version' => 'v1']
        );
        $this->makeJsonRequest($this->client, 'GET', $url, null, $this->getAuthenticatedUserToken());

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(2, $data['userId']);
    }
}
