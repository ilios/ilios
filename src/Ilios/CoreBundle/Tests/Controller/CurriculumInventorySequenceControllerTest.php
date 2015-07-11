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
        return [];
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
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            json_encode(['curriculumInventorySequence' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequences'][0],
            $response->getContent()
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
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCurriculumInventorySequence()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequences',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventorySequence' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
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
