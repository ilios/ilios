<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Permission controller Test.
 * @package Ilios\CoreBundle\Test\Controller
 */
class PermissionControllerTest extends AbstractControllerTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPermissionData',
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::getAction
     * @group controllers_b
     */
    public function testGetPermission()
    {
        $permission = $this->container
            ->get('ilioscore.dataloader.permission')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_permissions',
                ['id' => $permission['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($permission),
            json_decode($response->getContent(), true)['permissions'][0]
        );
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::cgetAction
     * @group controllers_b
     */
    public function testGetAllPermissions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_permissions'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.permission')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['permissions']
        );
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::postAction
     * @group controllers_b
     */
    public function testPostPermission()
    {
        $data = $this->container->get('ilioscore.dataloader.permission')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_permissions'),
            json_encode(['permission' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['permissions'][0],
            $response->getContent()
        );
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::postAction
     * @group controllers_b
     */
    public function testPostBadPermission()
    {
        $invalidPermission = $this->container
            ->get('ilioscore.dataloader.permission')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_permissions'),
            json_encode(['permission' => $invalidPermission]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::putAction
     * @group controllers_b
     */
    public function testPutPermission()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.permission')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_permissions',
                ['id' => $data['id']]
            ),
            json_encode(['permission' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['permissions']
        );
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::deleteAction
     * @group controllers
     */
    public function testDeletePermission()
    {
        $permission = $this->container
            ->get('ilioscore.dataloader.permission')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_permissions',
                ['id' => $permission['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_permissions',
                ['id' => $permission['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @covers Ilios\CoreBundle\Controller\PermissionController::getAction
     * @group controllers
     */
    public function testPermissionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_permissions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
