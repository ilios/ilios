<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * LearningMaterial controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearningMaterialControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialStatusData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'createdBy',
            'updatedBy'
        ];
    }

    public function testGetLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterials',
                ['id' => $learningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterial),
            json_decode($response->getContent(), true)['learningMaterials'][0]
        );
    }

    public function testGetAllLearningMaterials()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterials'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learningmaterial')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['learningMaterials']
        );
    }

    public function testPostLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterial')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learningMaterials'][0]
        );
    }

    public function testPostBadLearningMaterial()
    {
        $invalidLearningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $invalidLearningMaterial])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            json_encode(['learningMaterial' => $learningMaterial])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterial),
            json_decode($response->getContent(), true)['learningMaterial']
        );
    }

    public function testDeleteLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_learningmaterials',
                ['id' => $learningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_learningmaterials',
                ['id' => $learningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testLearningMaterialNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learningmaterials', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
