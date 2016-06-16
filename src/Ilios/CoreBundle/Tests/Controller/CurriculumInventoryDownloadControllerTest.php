<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CurriculumInventoryDownloadControllerTest
 * @package Ilios\CoreBundle\Tests\Controller
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
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData',
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
    }
}
