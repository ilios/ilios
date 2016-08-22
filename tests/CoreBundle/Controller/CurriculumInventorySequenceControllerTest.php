<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData'
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequence),
            json_decode($response->getContent(), true)['curriculumInventorySequences'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCurriculumInventorySequences()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequences'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_a
     */
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
            json_encode(['curriculumInventorySequence' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequences'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCurriculumInventorySequence()
    {
        $invalidCurriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequences'),
            json_encode(['curriculumInventorySequence' => $invalidCurriculumInventorySequence]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
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
            json_encode(['curriculumInventorySequence' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventorySequence']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventorySequence()
    {
        $curriculumInventorySequence = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequence')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequences',
                ['id' => $curriculumInventorySequence['report']]
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
    public function testCurriculumInventorySequenceNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequences', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
