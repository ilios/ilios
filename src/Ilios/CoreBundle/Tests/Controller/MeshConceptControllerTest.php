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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
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
            'umlsUid',
            'preferred',
            'scopeNote',
            'casn1Name',
            'registryNumber',
            'createdAt',
            'updatedAt'
        ];
    }

    public function testGetMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne()
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
            $this->mockSerialize($meshConcept),
            json_decode($response->getContent(), true)['meshConcepts'][0]
        );
    }

    public function testGetAllMeshConcepts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_meshconcepts'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.meshconcept')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['meshConcepts']
        );
    }

    public function testPostMeshConcept()
    {
        $data = $this->container->get('ilioscore.dataloader.meshconcept')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(['meshConcept' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['meshConcepts'][0]
        );
    }

    public function testPostBadMeshConcept()
    {
        $invalidMeshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(['meshConcept' => $invalidMeshConcept])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshconcepts',
                ['id' => $meshConcept['id']]
            ),
            json_encode(['meshConcept' => $meshConcept])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($meshConcept),
            json_decode($response->getContent(), true)['meshConcept']
        );
    }

    public function testDeleteMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne()
        ;

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
            $this->getUrl('get_meshconcepts', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
