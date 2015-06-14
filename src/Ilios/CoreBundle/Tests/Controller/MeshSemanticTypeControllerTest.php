<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * MeshSemanticType controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshSemanticTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshSemanticTypeData'
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

    public function testGetMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshSemanticType),
            json_decode($response->getContent(), true)['meshSemanticTypes'][0]
        );
    }

    public function testGetAllMeshSemanticTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshsemantictypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshsemantictype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshSemanticTypes']
        );
    }

    public function testPostMeshSemanticType()
    {
        $data = $this->container->get('ilioscore.dataloader.meshsemantictype')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(['meshSemanticType' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['meshSemanticTypes'][0]
        );
    }

    public function testPostBadMeshSemanticType()
    {
        $invalidMeshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(['meshSemanticType' => $invalidMeshSemanticType])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            ),
            json_encode(['meshSemanticType' => $meshSemanticType])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshSemanticType),
            json_decode($response->getContent(), true)['meshSemanticType']
        );
    }

    public function testDeleteMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshSemanticTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshsemantictypes', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
