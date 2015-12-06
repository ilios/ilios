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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
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
     * @group controllers_a
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlockSession),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCurriculumInventorySequenceBlockSessions()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocksessions'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_a
     */
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
            json_encode(['curriculumInventorySequenceBlockSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSessions'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCurriculumInventorySequenceBlockSession()
    {
        $invalidCurriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocksessions'),
            json_encode(['curriculumInventorySequenceBlockSession' => $invalidCurriculumInventorySequenceBlockSession]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
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
            json_encode(['curriculumInventorySequenceBlockSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlockSession']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventorySequenceBlockSession()
    {
        $curriculumInventorySequenceBlockSession = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblocksession')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocksessions',
                ['id' => $curriculumInventorySequenceBlockSession['id']]
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
    public function testCurriculumInventorySequenceBlockSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequenceblocksessions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
