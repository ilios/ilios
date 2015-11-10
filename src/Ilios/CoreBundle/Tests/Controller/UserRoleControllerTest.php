<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * UserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UserRoleControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return ['users'];
    }

    /**
     * @group controllers
     */
    public function testGetUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_userroles',
                ['id' => $userRole['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($userRole),
            json_decode($response->getContent(), true)['userRoles'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllUserRoles()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_userroles'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.userrole')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['userRoles']
        );
    }

    /**
     * @group controllers
     */
    public function testPostUserRole()
    {
        $data = $this->container->get('ilioscore.dataloader.userrole')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_userroles'),
            json_encode(['userRole' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['userRoles'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadUserRole()
    {
        $invalidUserRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_userroles'),
            json_encode(['userRole' => $invalidUserRole]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutUserRole()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['users']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_userroles',
                ['id' => $data['id']]
            ),
            json_encode(['userRole' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['userRole']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_userroles',
                ['id' => $userRole['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_userroles',
                ['id' => $userRole['id']]
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
    public function testUserRoleNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_userroles', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
