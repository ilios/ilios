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
 * @group controller
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Controller\ErrorController::class)]
class ErrorControllerTest extends WebTestCase
{
    use TestableJsonController;

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

    public function testPostError(): void
    {
        $data = [
            'mainMessage' => 'dev/null',
            'stack' => 'lorem ipsum',
        ];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/errors',
            json_encode(['data' => json_encode($data)]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());
    }

    public function testAnonymousAccessDenied(): void
    {
        $data = [
            'mainMessage' => 'lorem ipsum',
            'stack' => 'dev/null',
        ];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/errors',
            json_encode(['data' => json_encode($data)])
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testServiceTokenForbidden(): void
    {
        $data = [
            'mainMessage' => 'lorem ipsum',
            'stack' => 'dev/null',
        ];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/errors',
            json_encode(['data' => json_encode($data)]),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
