<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CurriculumInventoryDownloadControllerTest
 * @package Tests\CoreBundle\\Controller
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
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
        ]);
    }

    /**
     * @covers Ilios\CoreBundle\Controller\CurriculumInventoryDownloadController::getAction
     * @group controllers_a
     */
    public function testGetCurriculumInventoryDownload()
    {
        $client = $this->createClient();
        $curriculumInventoryExport = $client->getContainer()
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne()
        ;

        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryExport['report']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];

        $client->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $client->getResponse();
        $this->assertEquals($curriculumInventoryExport['document'], $response->getContent());
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $downloadCookie = null;
        $cookieName = 'report-download-' . $curriculumInventoryExport['report'];
        foreach($response->headers->getCookies() as $cookie) {
            if ($cookieName === $cookie->getName()) {
                $downloadCookie = $cookie;
                break;
            }
        }
        $this->assertNotNull($downloadCookie);
    }
}
