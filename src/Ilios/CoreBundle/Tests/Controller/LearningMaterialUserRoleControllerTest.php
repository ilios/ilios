<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * LearningMaterialUserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearningMaterialUserRoleControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialUserRoleData',
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

    public function testGetLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterialUserRole),
            json_decode($response->getContent(), true)['learningMaterialUserRoles'][0]
        );
    }

    public function testGetAllLearningMaterialUserRoles()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterialuserroles'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learningmaterialuserrole')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['learningMaterialUserRoles']
        );
    }

    public function testPostLearningMaterialUserRole()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterialuserrole')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            json_encode(['learningMaterialUserRole' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learningMaterialUserRoles'][0]
        );
    }

    public function testPostBadLearningMaterialUserRole()
    {
        $invalidLearningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            json_encode(['learningMaterialUserRole' => $invalidLearningMaterialUserRole])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            ),
            json_encode(['learningMaterialUserRole' => $learningMaterialUserRole])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterialUserRole),
            json_decode($response->getContent(), true)['learningMaterialUserRole']
        );
    }

    public function testDeleteLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testLearningMaterialUserRoleNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learningmaterialuserroles', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
