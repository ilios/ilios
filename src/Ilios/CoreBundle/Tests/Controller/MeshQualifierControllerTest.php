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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshQualifierData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'name',
            'createdAt',
            'updatedAt'
        ];
    }

    public function testGetMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne()
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
            $this->mockSerialize($meshQualifier),
            json_decode($response->getContent(), true)['meshQualifiers'][0]
        );
    }

    public function testGetAllMeshQualifiers()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshqualifiers'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshqualifier')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshQualifiers']
        );
    }

    public function testPostMeshQualifier()
    {
        $data = $this->container->get('ilioscore.dataloader.meshqualifier')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            json_encode(['meshQualifier' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
        );
    }

    public function testPostBadMeshQualifier()
    {
        $invalidMeshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            json_encode(['meshQualifier' => $invalidMeshQualifier])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshqualifiers',
                ['id' => $meshQualifier['id']]
            ),
            json_encode(['meshQualifier' => $meshQualifier])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshQualifier),
            json_decode($response->getContent(), true)['meshQualifier']
        );
    }

    public function testDeleteMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne()
        ;

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
            $this->getUrl('get_meshqualifiers', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
