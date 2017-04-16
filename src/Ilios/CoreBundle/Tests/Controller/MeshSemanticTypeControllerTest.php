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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshSemanticTypeData'
        ];
    }

    public function testGetMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->getOne()['meshSemanticType']
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
            $meshSemanticType,
            json_decode($response->getContent(), true)['meshSemanticType']
        );
    }

    public function testGetAllMeshSemanticTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshsemantictypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshsemantictype')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshSemanticType()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshsemantictype')
                    ->create()['meshSemanticType']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshSemanticType()
    {
        $invalidMeshSemanticType = array_shift(
            $this->container->get('ilioscore.dataloader.meshsemantictype')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            $invalidMeshSemanticType
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->createWithId()['meshSemanticType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            ),
            json_encode($meshSemanticType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshsemantictype')
                ->getLastCreated()['meshSemanticType'],
            json_decode($response->getContent(), true)['meshSemanticType']
        );
    }

    public function testDeleteMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemantictype')
            ->createWithId()['meshSemanticType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            ),
            json_encode($meshSemanticType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_meshsemantictypes', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
