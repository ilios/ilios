<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData;
use App\Tests\Fixture\LoadCurriculumInventoryExportData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceBlockData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\GetUrlTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTestable;

/**
 * Class CurriculumInventoryDownloadControllerTest
 */
class CurriculumInventoryDownloadControllerTest extends WebTestCase
{
    use JsonControllerTestable;
    use GetUrlTrait;

    protected KernelBrowser $kernelBrowser;
    protected string $apiVersion = 'v3';

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventoryExportData::class,
            LoadCurriculumInventoryInstitutionData::class,
            LoadCurriculumInventorySequenceData::class,
            LoadCurriculumInventorySequenceBlockData::class,
            LoadCurriculumInventoryAcademicLevelData::class,
            LoadSessionData::class,
            LoadAuthenticationData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
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
