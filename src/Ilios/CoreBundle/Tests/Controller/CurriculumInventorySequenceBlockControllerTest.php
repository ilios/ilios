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
     * @return array
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

    public function testGetCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()['curriculumInventorySequenceBlock']
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
            $curriculumInventorySequenceBlock,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlock']
        );
    }

    public function testGetAllCurriculumInventorySequenceBlocks()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequenceblocks'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventorySequenceBlock()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')
                    ->create()['curriculumInventorySequenceBlock']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventorySequenceBlock()
    {
        $invalidCurriculumInventorySequenceBlock = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            $invalidCurriculumInventorySequenceBlock
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->createWithId()['curriculumInventorySequenceBlock']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            ),
            json_encode($curriculumInventorySequenceBlock)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')
                ->getLastCreated()['curriculumInventorySequenceBlock'],
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlock']
        );
    }

    public function testDeleteCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->createWithId()['curriculumInventorySequenceBlock']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            ),
            json_encode($curriculumInventorySequenceBlock)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_curriculuminventorysequenceblocks', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
