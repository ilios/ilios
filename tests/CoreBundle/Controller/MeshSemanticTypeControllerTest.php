<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshSemanticTypeData',
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
    public function testGetMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemanticType')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshSemanticTypes'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($meshSemanticType),
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
    public function testGetAllMeshSemanticTypes()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshsemantictypes'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshSemanticTypes'];
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
                $this->container->get('ilioscore.dataloader.meshsemanticType')->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostMeshSemanticType()
    {
        $data = $this->container->get('ilioscore.dataloader.meshSemanticType')
            ->create();
        $postData = $data;
        
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(['meshSemanticType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        
        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshSemanticTypes', $response), var_export($response, true));
        $data = $response['meshSemanticTypes'][0];
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
    public function testPostMeshSemanticTypeConcept()
    {
        $data = $this->container->get('ilioscore.dataloader.meshsemantictype')->create();
        $postData = $data;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(['meshSemanticType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshSemanticTypes'][0]['id'];
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
            $this->assertTrue(in_array($newId, $data['semanticTypes']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostBadMeshSemanticType()
    {
        $invalidMeshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemanticType')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshsemantictypes'),
            json_encode(['meshSemanticType' => $invalidMeshSemanticType]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutMeshSemanticType()
    {
        $postData = $this->container
            ->get('ilioscore.dataloader.meshsemanticType')
            ->getOne();
        $postData['name'] = 'somethign new';
        //unset any parameters which should not be POSTed
        unset($postData['updatedAt']);
        unset($postData['descriptors']);
        unset($postData['createdAt']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshsemantictypes',
                ['id' => $postData['id']]
            ),
            json_encode(['meshSemanticType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshSemanticType'];
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
    public function testDeleteMeshSemanticType()
    {
        $meshSemanticType = $this->container
            ->get('ilioscore.dataloader.meshsemanticType')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshsemantictypes',
                ['id' => $meshSemanticType['id']]
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
    public function testMeshSemanticTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshsemantictypes', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
