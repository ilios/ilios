<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 */
class ConfigControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;

    /**
     * @var KernelBrowser
     */
    protected $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $this->loadFixtures([
            LoadApplicationConfigData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->fixtures);
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
