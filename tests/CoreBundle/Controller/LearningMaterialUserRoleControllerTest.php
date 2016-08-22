<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learningMaterialUserRole),
            json_decode($response->getContent(), true)['learningMaterialUserRoles'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllLearningMaterialUserRoles()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterialuserroles'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_a
     */
    public function testPostLearningMaterialUserRole()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterialuserrole')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            json_encode(['learningMaterialUserRole' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learningMaterialUserRoles'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadLearningMaterialUserRole()
    {
        $invalidLearningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterialuserroles'),
            json_encode(['learningMaterialUserRole' => $invalidLearningMaterialUserRole]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutLearningMaterialUserRole()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterialuserroles',
                ['id' => $data['id']]
            ),
            json_encode(['learningMaterialUserRole' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['learningMaterialUserRole']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteLearningMaterialUserRole()
    {
        $learningMaterialUserRole = $this->container
            ->get('ilioscore.dataloader.learningmaterialuserrole')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterialuserroles',
                ['id' => $learningMaterialUserRole['id']]
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
    public function testLearningMaterialUserRoleNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learningmaterialuserroles', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
