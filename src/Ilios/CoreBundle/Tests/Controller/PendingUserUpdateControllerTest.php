<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * PendingUserUpdate controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class PendingUserUpdateControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadPendingUserUpdateData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers
     */
    public function testGetPendingUserUpdate()
    {
        $pendingUserUpdate = $this->container
            ->get('ilioscore.dataloader.pendinguserupdate')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_pendinguserupdates',
                ['id' => $pendingUserUpdate['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($pendingUserUpdate),
            json_decode($response->getContent(), true)['pendingUserUpdates'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllPendingUserUpdates()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_pendinguserupdates'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.pendinguserupdate')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['pendingUserUpdates']
        );
    }

    /**
     * @group controllers
     */
    public function testPostPendingUserUpdate()
    {
        $data = $this->container->get('ilioscore.dataloader.pendinguserupdate')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_pendinguserupdates'),
            json_encode(['pendingUserUpdate' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['pendingUserUpdates'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadPendingUserUpdate()
    {
        $invalidPendingUserUpdate = $this->container
            ->get('ilioscore.dataloader.pendinguserupdate')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_pendinguserupdates'),
            json_encode(['pendingUserUpdate' => $invalidPendingUserUpdate]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutPendingUserUpdate()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.pendinguserupdate')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_pendinguserupdates',
                ['id' => $data['id']]
            ),
            json_encode(['pendingUserUpdate' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['pendingUserUpdate']
        );
    }

    /**
     * @group controllers
     */
    public function testDeletePendingUserUpdate()
    {
        $pendingUserUpdate = $this->container
            ->get('ilioscore.dataloader.pendinguserupdate')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_pendinguserupdates',
                ['id' => $pendingUserUpdate['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_pendinguserupdates',
                ['id' => $pendingUserUpdate['id']]
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
    public function testPendingUserUpdateNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_pendinguserupdates', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
