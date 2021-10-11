<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\TestVersionProvider;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\Traits\JsonControllerTest;

class ConfigControllerTest extends WebTestCase
{
    use JsonControllerTest;

    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadApplicationConfigData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testIndex()
    {
        $this->kernelBrowser->request('GET', '/application/config');
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('config', $content);
        $data = $content['config'];
        $this->assertArrayHasKey('maxUploadSize', $data);
        $this->assertGreaterThan(0, $data['maxUploadSize']);
        unset($data['maxUploadSize']);
        $container = $this->kernelBrowser->getContainer();

        $this->assertEquals(
            [
                'type' => 'form',
                'locale' => 'en',
                'userSearchType' => 'local',
                'apiVersion' => $container->getParameter('ilios_api_version'),
                'appVersion' => TestVersionProvider::VERSION,
                'trackingEnabled' => false,
                'searchEnabled' => false,
                'academicYearCrossesCalendarYearBoundaries' => false,
            ],
            $data
        );
    }

    public function testEnvOverrideForConfigItem()
    {
        $_SERVER['ILIOS_ENABLE_TRACKING'] = true;
        $_SERVER['ILIOS_TRACKING_CODE'] = '123-code!';

        $this->kernelBrowser->request('GET', '/application/config');

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('config', $content);
        $data = $content['config'];
        $this->assertArrayHasKey('maxUploadSize', $data);
        $this->assertGreaterThan(0, $data['maxUploadSize']);
        unset($data['maxUploadSize']);
        $container = $this->kernelBrowser->getContainer();

        $this->assertEquals(
            [
                'type' => 'form',
                'locale' => 'en',
                'userSearchType' => 'local',
                'apiVersion' => $container->getParameter('ilios_api_version'),
                'appVersion' => TestVersionProvider::VERSION,
                'trackingEnabled' => true,
                'trackingCode' => '123-code!',
                'searchEnabled' => false,
                'academicYearCrossesCalendarYearBoundaries' => false,
            ],
            $data
        );

        unset($_SERVER['ILIOS_ENABLE_TRACKING']);
        unset($_SERVER['ILIOS_TRACKING_CODE']);
    }
}
