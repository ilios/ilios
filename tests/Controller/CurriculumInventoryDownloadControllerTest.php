<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controller\CurriculumInventoryDownloadController;
use App\Tests\DataLoader\CurriculumInventoryExportData;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData;
use App\Tests\Fixture\LoadCurriculumInventoryExportData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceBlockData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceData;
use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\GetUrlTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\TestableJsonController;

#[Group('controller')]
#[CoversClass(CurriculumInventoryDownloadController::class)]
final class CurriculumInventoryDownloadControllerTest extends WebTestCase
{
    use TestableJsonController;
    use GetUrlTrait;

    protected KernelBrowser $kernelBrowser;
    protected string $apiVersion = 'v3';

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
            LoadCurriculumInventoryAcademicLevelData::class,
            LoadCurriculumInventoryExportData::class,
            LoadCurriculumInventoryInstitutionData::class,
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventorySequenceBlockData::class,
            LoadCurriculumInventorySequenceData::class,
            LoadServiceTokenData::class,
            LoadSessionData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testGetAction(): void
    {
        $jwts = [
            $this->createJwtFromUserId($this->kernelBrowser, 1),
            $this->createJwtForEnabledServiceToken($this->kernelBrowser),
        ];
        foreach ($jwts as $jwt) {
            $curriculumInventoryExport = $this->kernelBrowser->getContainer()
                ->get(CurriculumInventoryExportData::class)
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
                        'id' => $curriculumInventoryExport['report'],
                    ]
                ),
                null,
                $jwt
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
}
