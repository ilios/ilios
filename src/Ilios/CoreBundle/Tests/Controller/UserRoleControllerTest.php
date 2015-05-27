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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserRoleData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    public function testGetUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->getOne()['userRole']
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
            $userRole,
            json_decode($response->getContent(), true)['userRole']
        );
    }

    public function testGetAllUserRoles()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_userroles'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.userrole')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostUserRole()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_userroles'),
            json_encode(
                $this->container->get('ilioscore.dataloader.userrole')
                    ->create()['userRole']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadUserRole()
    {
        $invalidUserRole = array_shift(
            $this->container->get('ilioscore.dataloader.userrole')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_userroles'),
            $invalidUserRole
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->createWithId()['userRole']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_userroles',
                ['id' => $userRole['id']]
            ),
            json_encode($userRole)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.userrole')
                ->getLastCreated()['userRole'],
            json_decode($response->getContent(), true)['userRole']
        );
    }

    public function testDeleteUserRole()
    {
        $userRole = $this->container
            ->get('ilioscore.dataloader.userrole')
            ->createWithId()['userRole']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_userroles',
                ['id' => $userRole['id']]
            ),
            json_encode($userRole)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_userroles', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
