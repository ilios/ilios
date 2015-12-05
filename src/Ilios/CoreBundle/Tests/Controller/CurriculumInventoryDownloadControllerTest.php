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
     * @covers Ilios\CoreBundle\Controller\CurriculumInventoryDownloadController::getAction
     * @group controllers_a
     */
    public function testGetCurriculumInventoryDownload()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorydownloads',
                ['id' => $curriculumInventoryExport['report']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals($curriculumInventoryExport['document'], $response->getContent());
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode(), $response->getContent());
    }
}
