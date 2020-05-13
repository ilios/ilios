<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

class ApiControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    protected $apiVersion = 'v2';

    /**
     * @var KernelBrowser
     */
    protected $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->fixtures);
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
}
