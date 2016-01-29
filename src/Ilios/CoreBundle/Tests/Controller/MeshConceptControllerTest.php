<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshTermData',
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshConcepts'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($meshConcept),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');
    }

    /**
     * @group controllers_a
     */
    public function testGetAllMeshConcepts()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshconcepts'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshConcepts'];
        $now = new DateTime();
        $data = [];
        foreach ($responses as $response) {
            $updatedAt = new DateTime($response['updatedAt']);
            $createdAt = new DateTime($response['createdAt']);
            unset($response['updatedAt']);
            unset($response['createdAt']);
            $diffU = $now->diff($updatedAt);
            $diffC = $now->diff($createdAt);
            $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
            $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');
            $data[] = $response;
        }
        $this->assertEquals(
            $this->mockSerialize(
                $this->container->get('ilioscore.dataloader.meshconcept')->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostMeshConcept()
    {
        $data = $this->container->get('ilioscore.dataloader.meshConcept')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['updatedAt']);
        unset($postData['createdAt']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(['meshConcept' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshConcepts', $response), var_export($response, true));
        $data = $response['meshConcepts'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['updatedAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');

    }

    /**
     * @group controllers_a
     */
    public function testPostMeshConceptTerm()
    {
        $data = $this->container->get('ilioscore.dataloader.meshConcept')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['updatedAt']);
        unset($postData['createdAt']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(['meshConcept' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshConcepts'][0]['id'];
        foreach ($postData['terms'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_meshterms',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['meshTerms'][0];
            $this->assertTrue(in_array($newId, $data['concepts']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadMeshConcept()
    {
        $invalidMeshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshconcepts'),
            json_encode(['meshConcept' => $invalidMeshConcept]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutMeshConcept()
    {
        $postData = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne();
        $postData['scopeNote'] = 'somethign new';
        //unset any parameters which should not be POSTed
        unset($postData['updatedAt']);
        unset($postData['createdAt']);
        unset($postData['terms']);
        unset($postData['semanticTypes']);
        unset($postData['descriptors']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshconcepts',
                ['id' => $postData['id']]
            ),
            json_encode(['meshConcept' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshConcept'];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $this->assertTrue($diffU->m < 1, 'The updatedAt timestamp is within the last minute');
    }

    /**
     * @group controllers
     */
    public function testDeleteMeshConcept()
    {
        $meshConcept = $this->container
            ->get('ilioscore.dataloader.meshconcept')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshconcepts',
                ['id' => $meshConcept['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshconcepts',
                ['id' => $meshConcept['id']]
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
    public function testMeshConceptNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshconcepts', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
