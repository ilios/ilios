<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadAuthenticationData;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTestable;

class ApiControllerTest extends WebTestCase
{
    use JsonControllerTestable;

    protected string $apiVersion = 'v3';
    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testNoEndpoint()
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            "/api/{$this->apiVersion}/nothing",
            null,
            $this->getTokenForUser($this->kernelBrowser, 1)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testNoVersion()
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            '/api/nothing',
            null,
            $this->getTokenForUser($this->kernelBrowser, 1)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testBadVersion()
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            '/api/1/courses',
            null,
            $this->getTokenForUser($this->kernelBrowser, 1)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testApiInfoAuthenticated()
    {
        $this->kernelBrowser->request(
            'GET',
            '/api',
            [],
            [],
            ['HTTP_X-JWT-Authorization' => 'Token ' . $this->getTokenForUser($this->kernelBrowser, 1)],
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('<h1>API Info</h1>', $response->getContent());
    }

    public function testApiInfoNotAuthenticated()
    {
        $this->kernelBrowser->request(
            'GET',
            '/api',
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('<h1>API Info</h1>', $response->getContent());
    }
}
