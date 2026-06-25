<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadServiceTokenData;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\TestableJsonController;

/**
 * GraphQL API endpoint tests.
 */
#[Group('api_graphql')]
final class GraphQLTest extends WebTestCase
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

    public function testGraphQLAuthenticated(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        $query = ['query' => 'query { schools { id title } }'];

        foreach ($jwts as $jwt) {
            $this->kernelBrowser->request(
                'POST',
                '/api/graphql',
                [],
                [],
                [
                    'Content-Type' => 'application/json',
                    'HTTP_X-JWT-Authorization' => 'Token ' . $jwt,
                ],
                json_encode($query),
            );

            $response = $this->kernelBrowser->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertStringContainsString('{"data":{"schools', $response->getContent());
        }
    }

    public function testGraphQLNotAuthenticated(): void
    {

        $this->kernelBrowser->request(
            'GET',
            '/api/graphql',
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
