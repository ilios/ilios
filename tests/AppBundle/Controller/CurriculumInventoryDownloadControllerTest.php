<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Traits\JsonControllerTest;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CurriculumInventoryDownloadControllerTest
 */
class CurriculumInventoryDownloadControllerTest extends WebTestCase
{
    use JsonControllerTest;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->loadFixtures([
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventorySequenceData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\AppBundle\Fixture\LoadSessionData',
            'Tests\AppBundle\Fixture\LoadAuthenticationData',
        ]);
    }

    /**
     * @covers \AppBundle\Controller\CurriculumInventoryDownloadController::getAction
     */
    public function testGetCurriculumInventoryDownload()
    {
        $client = $this->createClient();
        $curriculumInventoryExport = $client->getContainer()
            ->get('Tests\AppBundle\DataLoader\CurriculumInventoryExportData')
            ->getOne()
        ;

        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                'ilios_api_get',
                [
                    'version' => 'v1',
                    'object' => 'curriculuminventoryreports',
                    'id' => $curriculumInventoryExport['report']
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];

        $client->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $client->getResponse();
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
