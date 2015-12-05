<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * MeshPreviousIndexing controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshPreviousIndexingControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshPreviousIndexingData',
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
    public function testGetMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousIndexing')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshPreviousIndexings'][0];
        
        $this->assertEquals(
            $this->mockSerialize($meshPreviousIndexing),
            $data
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllMeshPreviousIndexings()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshpreviousindexings'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshPreviousIndexings'];
        $this->assertEquals(
            $this->mockSerialize(
                $this->container->get('ilioscore.dataloader.meshpreviousIndexing')->getAll()
            ),
            $responses
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostMeshPreviousIndexing()
    {
        $data = $this->container->get('ilioscore.dataloader.meshPreviousIndexing')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            json_encode(['meshPreviousIndexing' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        
        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshPreviousIndexings', $response), var_export($response, true));
        $data = $response['meshPreviousIndexings'][0];
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadMeshPreviousIndexing()
    {
        $invalidMeshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousIndexing')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            json_encode(['meshPreviousIndexing' => $invalidMeshPreviousIndexing]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutMeshPreviousIndexing()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.meshpreviousIndexing')
            ->getOne();
        $data['previousIndexing'] = 'somethign new';
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshpreviousindexings',
                ['id' => $data['id']]
            ),
            json_encode(['meshPreviousIndexing' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $response = json_decode($response->getContent(), true)['meshPreviousIndexing'];
        $this->assertEquals(
            $this->mockSerialize($data),
            $response
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousIndexing')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['id']]
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
    public function testMeshPreviousIndexingNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshpreviousindexings', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
