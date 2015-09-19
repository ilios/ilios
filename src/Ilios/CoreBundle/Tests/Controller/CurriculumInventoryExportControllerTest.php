<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryExport controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryExportControllerTest extends AbstractControllerTest
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
     * @covers CurriculumInventoryExportController::postAction
     */
    public function testPostCurriculumInventoryExport()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryexport')->create();
        $postData = $data;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            json_encode(['curriculumInventoryExport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
    }
}
