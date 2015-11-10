<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryReport controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryReportControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers
     */
    public function testGetCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryReport),
            json_decode($response->getContent(), true)['curriculumInventoryReports'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllCurriculumInventoryReports()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventoryreports'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $expected = $this->mockSerialize(
            $this->container
                ->get('ilioscore.dataloader.curriculuminventoryreport')
                ->getAll()
        );
        $actual = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /**
     * @group controllers
     */
    public function testPostCurriculumInventoryReport()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryreport')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequenceBlocks']);
        unset($postData['academicLevels']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventoryReports'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadCurriculumInventoryReport()
    {
        $invalidCurriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $invalidCurriculumInventoryReport]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutCurriculumInventoryReport()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequenceBlocks']);
        unset($postData['academicLevels']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryreports',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventoryReport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventoryReport']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testCurriculumInventoryReportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryreports', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
