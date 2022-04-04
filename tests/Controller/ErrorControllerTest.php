<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadAuthenticationData;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;
use Faker\Factory as FakerFactory;

class ErrorControllerTest extends WebTestCase
{
    use JsonControllerTest;

    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testIndex()
    {
        $data = [
            'mainMessage' => 'dev/null',
            'stack' => 'lorem ipsum'
        ];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            '/errors',
            json_encode(['data' => json_encode($data)]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode(), $response->getContent());
    }

    public function testAnonymousAccessDenied()
    {
        $data = [
            'mainMessage' => 'lorem ipsum',
            'stack' => 'dev/null'
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
}
