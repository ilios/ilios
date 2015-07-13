<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequenceBlockSession controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceBlockSessionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    public function testGetCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlockSession),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions'][0]
        );
    }

    public function testGetAllCurriculumInventorySequenceBlockSessions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventorysequenceblocksessions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions']
        );
    }

    public function testPostCurriculumInventorySequenceBlockSession()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(['curriculumInventorySequenceBlockSession' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions'][0],
            $response->getContent()
        );
    }

    public function testPostBadCurriculumInventorySequenceBlockSession()
    {
        $invalidCurriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(['curriculumInventorySequenceBlockSession' => $invalidCurriculumInventorySequenceBlockSession])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCurriculumInventorySequenceBlockSession()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocksessions',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventorySequenceBlockSession' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSession']
        );
    }

    public function testDeleteCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventorySequenceBlockSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequenceblocksessions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
