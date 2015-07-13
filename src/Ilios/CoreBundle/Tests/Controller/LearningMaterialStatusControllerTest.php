<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * LearningMaterialStatus controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearningMaterialStatusControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialStatusData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetLearningMaterialStatus()
    {
        $learningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterialstatuses',
                ['id' => $learningMaterialStatus['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterialStatus),
            json_decode($response->getContent(), true)['learningMaterialStatuses'][0]
        );
    }

    public function testGetAllLearningMaterialStatuses()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterialstatuses'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learningmaterialstatus')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['learningMaterialStatuses']
        );
    }

    public function testPostLearningMaterialStatus()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterialstatus')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialstatuses'),
            json_encode(['learningMaterialStatus' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learningMaterialStatuses'][0],
            $response->getContent()
        );
    }

    public function testPostBadLearningMaterialStatus()
    {
        $invalidLearningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialstatuses'),
            json_encode(['learningMaterialStatus' => $invalidLearningMaterialStatus])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutLearningMaterialStatus()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialstatuses',
                ['id' => $data['id']]
            ),
            json_encode(['learningMaterialStatus' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['learningMaterialStatus']
        );
    }

    public function testDeleteLearningMaterialStatus()
    {
        $learningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_learningmaterialstatuses',
                ['id' => $learningMaterialStatus['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_learningmaterialstatuses',
                ['id' => $learningMaterialStatus['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testLearningMaterialStatusNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learningmaterialstatuses', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
