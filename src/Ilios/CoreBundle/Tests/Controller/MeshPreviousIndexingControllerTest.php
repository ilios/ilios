<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshPreviousIndexingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'previousIndexing'
        ];
    }

    public function testGetMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshPreviousIndexing),
            json_decode($response->getContent(), true)['meshPreviousIndexings'][0]
        );
    }

    public function testGetAllMeshPreviousIndexings()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshpreviousindexings'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshpreviousindexing')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshPreviousIndexings']
        );
    }

    public function testPostMeshPreviousIndexing()
    {
        $data = $this->container->get('ilioscore.dataloader.meshpreviousindexing')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            json_encode(['meshPreviousIndexing' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['meshPreviousIndexings'][0]
        );
    }

    public function testPostBadMeshPreviousIndexing()
    {
        $invalidMeshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            json_encode(['meshPreviousIndexing' => $invalidMeshPreviousIndexing])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            ),
            json_encode(['meshPreviousIndexing' => $meshPreviousIndexing])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshPreviousIndexing),
            json_decode($response->getContent(), true)['meshPreviousIndexing']
        );
    }

    public function testDeleteMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshPreviousIndexingNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshpreviousindexings', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
