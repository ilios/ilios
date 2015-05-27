<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequenceBlock controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceBlockControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'title',
            'description',
            'required',
            'childSequenceOrder',
            'orderInSequence',
            'minimum',
            'maximum',
            'track',
            'startDate',
            'endDate',
            'duration'
        ];
    }

    public function testGetCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlock),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0]
        );
    }

    public function testGetAllCurriculumInventorySequenceBlocks()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequenceblocks'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks']
        );
    }

    public function testPostCurriculumInventorySequenceBlock()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $data])
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

    public function testPostBadCurriculumInventorySequenceBlock()
    {
        $invalidCurriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $invalidCurriculumInventorySequenceBlock])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $curriculumInventorySequenceBlock])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlock),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlock']
        );
    }

    public function testDeleteCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventorySequenceBlockNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequenceblocks', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
