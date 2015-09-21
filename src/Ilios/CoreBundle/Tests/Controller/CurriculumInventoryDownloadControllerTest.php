<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Class CurriculumInventoryDownloadControllerTest
 * @package Ilios\CoreBundle\Tests\Controller
 */
class CurriculumInventoryDownloadControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'createdAt'
        ];
    }

    /**
     * @covers CurriculumInventoryDownloadController::getAction
     */
    public function testGetCurriculumInventoryAcademicLevel()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexports')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorydownloads',
                ['report' => $curriculumInventoryExport['report']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
    }
}
