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
     * @return array
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

    public function testGetLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()['learningMaterial']
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
            $learningMaterial,
            json_decode($response->getContent(), true)['learningMaterial']
        );
    }

    public function testGetAllLearningMaterials()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterials'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterial')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostLearningMaterial()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(
                $this->container->get('ilioscore.dataloader.learningmaterial')
                    ->create()['learningMaterial']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadLearningMaterial()
    {
        $invalidLearningMaterial = array_shift(
            $this->container->get('ilioscore.dataloader.learningmaterial')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            $invalidLearningMaterial
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->createWithId()['learningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            json_encode($learningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterial')
                ->getLastCreated()['learningMaterial'],
            json_decode($response->getContent(), true)['learningMaterial']
        );
    }

    public function testDeleteLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->createWithId()['learningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            json_encode($learningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_learningmaterials', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
