<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequence controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'description'
        ];
    }

    public function testGetCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequence),
            json_decode($response->getContent(), true)['curriculumInventorySequences'][0]
        );
    }

    public function testGetAllCurriculumInventorySequences()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequences'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventorysequence')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventorySequences']
        );
    }

    public function testPostCurriculumInventorySequence()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventorysequence')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            json_encode(['curriculumInventorySequence' => $data])
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

    public function testPostBadCurriculumInventorySequence()
    {
        $invalidCurriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            json_encode(['curriculumInventorySequence' => $invalidCurriculumInventorySequence])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            ),
            json_encode(['curriculumInventorySequence' => $curriculumInventorySequence])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequence),
            json_decode($response->getContent(), true)['curriculumInventorySequence']
        );
    }

    public function testDeleteCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventorySequenceNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequences', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
