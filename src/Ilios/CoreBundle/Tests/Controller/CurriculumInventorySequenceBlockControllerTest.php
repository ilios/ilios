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
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
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
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0],
            $response->getContent()
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
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCurriculumInventorySequenceBlock()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
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
