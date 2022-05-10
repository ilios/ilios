<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\GetUrlTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
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
    use GetUrlTrait;

    protected KernelBrowser $kernelBrowser;
    protected string $apiVersion = 'v3';

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
            LoadUserData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetCurrentSession()
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_currentsession_getcurrentsession',
            ['version' => $this->apiVersion]
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
    public function testAccessDeniedForAnonymousUser()
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_currentsession_getcurrentsession',
            ['version' => $this->apiVersion]
        );
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $url,
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }
}
