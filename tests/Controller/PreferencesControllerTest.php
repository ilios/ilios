<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controller\PreferencesController;
use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\Fixture\LoadUserPreferenceData;
use App\Tests\Traits\TestableJsonController;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

#[Group('controller')]
#[CoversClass(PreferencesController::class)]
final class PreferencesControllerTest extends WebTestCase
{
    use TestableJsonController;

    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadUserData::class,
            LoadUserPreferenceData::class,
            LoadServiceTokenData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testGetPreferences(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 1);
        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, $jwt);
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertSame(
            ['theme' => 'dark', 'locale' => 'en'],
            json_decode($response->getContent(), true)
        );
    }

    public function testGetPreferencesReturnsNotFoundWhenNoneExist(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 2);
        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, $jwt);
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND, false);
    }

    public function testGetPreferencesRequiresAuthentication(): void
    {
        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, null);
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testGetPreferencesWithServiceTokenIsForbidden(): void
    {
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, $jwt);
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPutPreferencesUpdatesExisting(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 1);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            '/application/preferences',
            '{"theme":"light","locale":"fr"}',
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertSame(
            ['theme' => 'light', 'locale' => 'fr'],
            json_decode($response->getContent(), true)
        );

        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, $jwt);
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertSame(
            ['theme' => 'light', 'locale' => 'fr'],
            json_decode($response->getContent(), true)
        );
    }

    public function testPutPreferencesCreatesWhenNoneExist(): void
    {
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 2);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            '/application/preferences',
            '{"theme":"dark"}',
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertSame(['theme' => 'dark'], json_decode($response->getContent(), true));

        $this->makeJsonRequest($this->kernelBrowser, 'GET', '/application/preferences', null, $jwt);
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $this->assertSame(
            ['theme' => 'dark'],
            json_decode($response->getContent(), true)
        );
    }

    public function testPutPreferencesRequiresAuthentication(): void
    {
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            '/application/preferences',
            '{"theme":"light"}',
            null
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPutPreferencesWithServiceTokenIsForbidden(): void
    {
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            '/application/preferences',
            '{"theme":"light"}',
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
