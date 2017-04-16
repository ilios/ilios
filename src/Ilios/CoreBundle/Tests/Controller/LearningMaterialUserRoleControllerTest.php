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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData'
        ];
    }

    public function testGetLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne()['learningMaterialUserRole']
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
            $learningMaterialUserRole,
            json_decode($response->getContent(), true)['learningMaterialUserRole']
        );
    }

    public function testGetAllLearningMaterialUserRoles()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learningmaterialuserroles'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterialuserrole')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostLearningMaterialUserRole()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            json_encode(
                $this->container->get('ilioscore.dataloader.learningmaterialuserrole')
                    ->create()['learningMaterialUserRole']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadLearningMaterialUserRole()
    {
        $invalidLearningMaterialUserRole = array_shift(
            $this->container->get('ilioscore.dataloader.learningmaterialuserrole')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            $invalidLearningMaterialUserRole
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->createWithId()['learningMaterialUserRole']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            ),
            json_encode($learningMaterialUserRole)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.learningmaterialuserrole')
                ->getLastCreated()['learningMaterialUserRole'],
            json_decode($response->getContent(), true)['learningMaterialUserRole']
        );
    }

    public function testDeleteLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->createWithId()['learningMaterialUserRole']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            ),
            json_encode($learningMaterialUserRole)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_learningmaterialuserroles', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
