<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * MeshConcept controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshConceptControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    public function testGetMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne()['meshConcept']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshconcepts',
                ['id' => $meshConcept['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $meshConcept,
            json_decode($response->getContent(), true)['meshConcept']
        );
    }

    public function testGetAllMeshConcepts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshconcepts'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshconcept')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostMeshConcept()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(
                $this->container->get('ilioscore.dataloader.meshconcept')
                    ->create()['meshConcept']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadMeshConcept()
    {
        $invalidMeshConcept = array_shift(
            $this->container->get('ilioscore.dataloader.meshconcept')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            $invalidMeshConcept
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->createWithId()['meshConcept']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshconcepts',
                ['id' => $meshConcept['id']]
            ),
            json_encode($meshConcept)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.meshconcept')
                ->getLastCreated()['meshConcept'],
            json_decode($response->getContent(), true)['meshConcept']
        );
    }

    public function testDeleteMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->createWithId()['meshConcept']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshconcepts',
                ['id' => $meshConcept['id']]
            ),
            json_encode($meshConcept)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_meshconcepts',
                ['id' => $meshConcept['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_meshconcepts',
                ['id' => $meshConcept['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMeshConceptNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshconcepts', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
