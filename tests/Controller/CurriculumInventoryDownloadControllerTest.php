<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\GetUrlTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

/**
 * Class CurriculumInventoryDownloadControllerTest
 */
class CurriculumInventoryDownloadControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;
    use GetUrlTrait;

    /**
     * @var KernelBrowser
     */
    protected $kernelBrowser;

    protected $apiVersion = 'v3';

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $this->loadFixtures([
            'App\Tests\Fixture\LoadCurriculumInventoryReportData',
            'App\Tests\Fixture\LoadCurriculumInventoryExportData',
            'App\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'App\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'App\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'App\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadAuthenticationData',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->fixtures);
    }

    /**
     * @covers \App\Controller\CurriculumInventoryDownloadController::getAction
     */
    public function testGetCurriculumInventoryDownload()
    {
        $curriculumInventoryExport = $this->kernelBrowser->getContainer()
            ->get('App\Tests\DataLoader\CurriculumInventoryExportData')
            ->getOne()
        ;

        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_getone',
                [
                    'version' => $this->apiVersion,
                    'id' => $curriculumInventoryExport['report']
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];

        $this->kernelBrowser->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals($curriculumInventoryExport['document'], $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $downloadCookie = null;
        $cookieName = 'report-download-' . $curriculumInventoryExport['report'];
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookieName === $cookie->getName()) {
                $downloadCookie = $cookie;
                break;
            }
        }
        $this->assertNotNull($downloadCookie);
    }
}
