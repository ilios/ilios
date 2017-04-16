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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshPreviousIndexingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    public function testGetMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->getOne()['meshPreviousIndexing']
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
            $meshPreviousIndexing,
            json_decode($response->getContent(), true)['meshPreviousIndexing']
        );
    }

    public function testGetAllMeshPreviousIndexings()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshpreviousindexings'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshpreviousindexing')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshPreviousIndexing()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshpreviousindexing')
                    ->create()['meshPreviousIndexing']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshPreviousIndexing()
    {
        $invalidMeshPreviousIndexing = array_shift(
            $this->container->get('ilioscore.dataloader.meshpreviousindexing')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshpreviousindexings'),
            $invalidMeshPreviousIndexing
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->createWithId()['meshPreviousIndexing']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            ),
            json_encode($meshPreviousIndexing)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshpreviousindexing')
                ->getLastCreated()['meshPreviousIndexing'],
            json_decode($response->getContent(), true)['meshPreviousIndexing']
        );
    }

    public function testDeleteMeshPreviousIndexing()
    {
        $meshPreviousIndexing = $this->container
            ->get('ilioscore.dataloader.meshpreviousindexing')
            ->createWithId()['meshPreviousIndexing']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshpreviousindexings',
                ['id' => $meshPreviousIndexing['descriptor']]
            ),
            json_encode($meshPreviousIndexing)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_meshpreviousindexings', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
