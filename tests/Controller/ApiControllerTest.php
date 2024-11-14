<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadServiceTokenData;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\TestableJsonController;

/**
 * General API tests.
 */
#[\PHPUnit\Framework\Attributes\Group('controller')]
class ApiControllerTest extends WebTestCase
{
    use TestableJsonController;

    protected string $apiVersion = 'v3';

    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
            LoadServiceTokenData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testNoEndpoint(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        foreach ($jwts as $jwt) {
            $this->makeJsonRequest(
                $this->kernelBrowser,
                'GET',
                "/api/$this->apiVersion/nothing",
                null,
                $jwt
            );
            $response = $this->kernelBrowser->getResponse();
            $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
        }
    }

    public function testNoVersion(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        foreach ($jwts as $jwt) {
            $this->makeJsonRequest(
                $this->kernelBrowser,
                'GET',
                '/api/nothing',
                null,
                $jwt
            );
            $response = $this->kernelBrowser->getResponse();
            $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
        }
    }

    public function testBadVersion(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        foreach ($jwts as $jwt) {
            $this->makeJsonRequest(
                $this->kernelBrowser,
                'GET',
                '/api/1/courses',
                null,
                $jwt
            );
            $response = $this->kernelBrowser->getResponse();
            $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
        }
    }

    public function testApiInfoAuthenticated(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        foreach ($jwts as $jwt) {
            $this->kernelBrowser->request(
                'GET',
                '/api',
                [],
                [],
                ['HTTP_X-JWT-Authorization' => 'Token ' . $jwt],
            );

            $response = $this->kernelBrowser->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertStringContainsString('<h1>API Info</h1>', $response->getContent());
        }
    }

    public function testApiInfoNotAuthenticated(): void
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
