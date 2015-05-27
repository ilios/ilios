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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'name',
            'description',
            'year',
            'startDate',
            'endDate'
        ];
    }

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
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryReport),
            json_decode($response->getContent(), true)['curriculumInventoryReports'][0]
        );
    }

    public function testGetAllCurriculumInventoryReports()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryreports'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventoryreport')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventoryReports']
        );
    }

    public function testPostCurriculumInventoryReport()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryreport')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
        );
    }

    public function testPostBadCurriculumInventoryReport()
    {
        $invalidCurriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $invalidCurriculumInventoryReport])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            json_encode(['curriculumInventoryReport' => $curriculumInventoryReport])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryReport),
            json_decode($response->getContent(), true)['curriculumInventoryReport']
        );
    }

    public function testDeleteCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventoryReportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryreports', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
