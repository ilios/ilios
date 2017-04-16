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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialStatusData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData'
        ];
    }

    public function testGetLearningMaterialStatus()
    {
        $learningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->getOne()['learningMaterialStatus']
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
            $learningMaterialStatus,
            json_decode($response->getContent(), true)['learningMaterialStatus']
        );
    }

    public function testGetAllLearningMaterialStatuses()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterialstatuses'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterialstatus')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostLearningMaterialStatus()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialstatuses'),
            json_encode(
                $this->container->get('ilioscore.dataloader.learningmaterialstatus')
                    ->create()['learningMaterialStatus']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadLearningMaterialStatus()
    {
        $invalidLearningMaterialStatus = array_shift(
            $this->container->get('ilioscore.dataloader.learningmaterialstatus')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialstatuses'),
            $invalidLearningMaterialStatus
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutLearningMaterialStatus()
    {
        $learningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->createWithId()['learningMaterialStatus']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialstatuses',
                ['id' => $learningMaterialStatus['id']]
            ),
            json_encode($learningMaterialStatus)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterialstatus')
                ->getLastCreated()['learningMaterialStatus'],
            json_decode($response->getContent(), true)['learningMaterialStatus']
        );
    }

    public function testDeleteLearningMaterialStatus()
    {
        $learningMaterialStatus = $this->container
            ->get('ilioscore.dataloader.learningmaterialstatus')
            ->createWithId()['learningMaterialStatus']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialstatuses',
                ['id' => $learningMaterialStatus['id']]
            ),
            json_encode($learningMaterialStatus)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_learningmaterialstatuses', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
