<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * MeshQualifier controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshQualifierControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshQualifierData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    public function testGetMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne()['meshQualifier']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshqualifiers',
                ['id' => $meshQualifier['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $meshQualifier,
            json_decode($response->getContent(), true)['meshQualifier']
        );
    }

    public function testGetAllMeshQualifiers()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshqualifiers'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshqualifier')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshQualifier()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshqualifier')
                    ->create()['meshQualifier']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshQualifier()
    {
        $invalidMeshQualifier = array_shift(
            $this->container->get('ilioscore.dataloader.meshqualifier')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            $invalidMeshQualifier
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->createWithId()['meshQualifier']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshqualifiers',
                ['id' => $meshQualifier['id']]
            ),
            json_encode($meshQualifier)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshqualifier')
                ->getLastCreated()['meshQualifier'],
            json_decode($response->getContent(), true)['meshQualifier']
        );
    }

    public function testDeleteMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->createWithId()['meshQualifier']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshqualifiers',
                ['id' => $meshQualifier['id']]
            ),
            json_encode($meshQualifier)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshqualifiers',
                ['id' => $meshQualifier['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshqualifiers',
                ['id' => $meshQualifier['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshQualifierNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshqualifiers', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
