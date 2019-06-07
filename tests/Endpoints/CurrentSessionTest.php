<?php

namespace App\Tests\Endpoints;

use App\Tests\GetUrlTrait;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    use GetUrlTrait;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;

    /**
     * @var KernelBrowser
     */
    protected $kernelBrowser;


    public function setUp()
    {
        $this->kernelBrowser = self::createClient();
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
        $url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_currentsession',
            ['version' => 'v1']
        );
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(2, $data['userId']);
    }
}
