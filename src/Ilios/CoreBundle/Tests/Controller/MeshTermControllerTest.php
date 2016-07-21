<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * MeshTerm controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshTermControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshTermData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
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
     * @group controllers_b
     */
    public function testGetMeshTerm()
    {
        $meshTerm = $this->container
            ->get('ilioscore.dataloader.meshterm')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshterms',
                ['id' => $meshTerm['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshTerms'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($meshTerm),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');
    }

    /**
     * @group controllers_b
     */
    public function testGetAllMeshTerms()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshterms'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshTerms'];
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
                $this->container->get('ilioscore.dataloader.meshterm')->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostMeshTerm()
    {
        $data = $this->container->get('ilioscore.dataloader.meshTerm')->create();
        $postData = $data;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshterms'),
            json_encode(['meshTerm' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshTerms', $response), var_export($response, true));
        $data = $response['meshTerms'][0];
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
     * @group controllers_b
     */
    public function testPostMeshTermConcept()
    {
        $data = $this->container->get('ilioscore.dataloader.meshterm')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshterms'),
            json_encode(['meshTerm' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshTerms'][0]['id'];
        foreach ($postData['concepts'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_meshconcepts',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['meshConcepts'][0];
            $this->assertTrue(in_array($newId, $data['terms']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostBadMeshTerm()
    {
        $invalidMeshTerm = $this->container
            ->get('ilioscore.dataloader.meshterm')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshterms'),
            json_encode(['meshTerm' => $invalidMeshTerm]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutMeshTerm()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.meshterm')
            ->getOne();
        $postData['name'] = 'somethign new';
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['updatedAt']);
        unset($postData['concepts']);
        unset($postData['createdAt']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshterms',
                ['id' => $data['id']]
            ),
            json_encode(['meshTerm' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshTerm'];
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
    public function testDeleteMeshTerm()
    {
        $meshTerm = $this->container
            ->get('ilioscore.dataloader.meshterm')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshterms',
                ['id' => $meshTerm['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshterms',
                ['id' => $meshTerm['id']]
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
    public function testMeshTermNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshterms', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
