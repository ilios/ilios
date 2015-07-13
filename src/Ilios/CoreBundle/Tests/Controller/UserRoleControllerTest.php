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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
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
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($userRole),
            json_decode($response->getContent(), true)['userRoles'][0]
        );
    }

    public function testGetAllUserRoles()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_userroles'));
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
            json_encode(['userRole' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['userRoles'][0],
            $response->getContent()
        );
    }

    public function testPostBadUserRole()
    {
        $invalidUserRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_userroles'),
            json_encode(['userRole' => $invalidUserRole])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutUserRole()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_userroles',
                ['id' => $data['id']]
            ),
            json_encode(['userRole' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['userRole']
        );
    }

    public function testDeleteUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_userroles',
                ['id' => $userRole['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_userroles',
                ['id' => $userRole['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testUserRoleNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_userroles', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
