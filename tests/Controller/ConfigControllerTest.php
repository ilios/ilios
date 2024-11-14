<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\Traits\TestableJsonController;
use Composer\InstalledVersions;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group controller
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Controller\ConfigController::class)]
class ConfigControllerTest extends WebTestCase
{
    use TestableJsonController;

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

    public function testGetConfig(): void
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

        $this->assertSame(
            [
                'type' => 'form',
                'locale' => 'en',
                'userSearchType' => 'local',
                'apiVersion' => $container->getParameter('ilios_api_version'),
                'appVersion' => InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']),
                'trackingEnabled' => false,
                'searchEnabled' => false,
                'academicYearCrossesCalendarYearBoundaries' => false,
                'materialStatusEnabled' => false,
                'showCampusNameOfRecord' => false,
            ],
            $data
        );
    }

    public function testEnvOverrideForConfigItem(): void
    {
        $_SERVER['ILIOS_ACADEMIC_YEAR_CROSSES_CALENDAR_YEAR_BOUNDARIES'] = true;
        $_SERVER['ILIOS_MATERIAL_STATUS_ENABLED'] = true;
        $_SERVER['ILIOS_SHOW_CAMPUS_NAME_OF_RECORD'] = true;

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

        $this->assertSame(
            [
                'type' => 'form',
                'locale' => 'en',
                'userSearchType' => 'local',
                'apiVersion' => $container->getParameter('ilios_api_version'),
                'appVersion' => InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']),
                'trackingEnabled' => false,
                'searchEnabled' => false,
                'academicYearCrossesCalendarYearBoundaries' => true,
                'materialStatusEnabled' => true,
                'showCampusNameOfRecord' => true,
            ],
            $data
        );

        unset($_SERVER['ILIOS_ACADEMIC_YEAR_CROSSES_CALENDAR_YEAR_BOUNDARIES']);
        unset($_SERVER['ILIOS_MATERIAL_STATUS_ENABLED']);
        unset($_SERVER['ILIOS_SHOW_CAMPUS_NAME_OF_RECORD']);
    }
}
