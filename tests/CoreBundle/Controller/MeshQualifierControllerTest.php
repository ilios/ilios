<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadMeshQualifierData',
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshQualifiers'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($meshQualifier),
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
    public function testGetAllMeshQualifiers()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshqualifiers'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshQualifiers'];
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
                $this->container->get('ilioscore.dataloader.meshqualifier')->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostMeshQualifier()
    {
        $data = $this->container->get('ilioscore.dataloader.meshQualifier')
            ->create();
        $postData = $data;
        
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            json_encode(['meshQualifier' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        
        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshQualifiers', $response), var_export($response, true));
        $data = $response['meshQualifiers'][0];
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
    public function testPostBadMeshQualifier()
    {
        $invalidMeshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshqualifiers'),
            json_encode(['meshQualifier' => $invalidMeshQualifier]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutMeshQualifier()
    {
        $postData = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne();
        $postData['name'] = 'somethign new';
        //unset any parameters which should not be POSTed
        unset($postData['updatedAt']);
        unset($postData['descriptors']);
        unset($postData['createdAt']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshqualifiers',
                ['id' => $postData['id']]
            ),
            json_encode(['meshQualifier' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshQualifier'];
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
    public function testDeleteMeshQualifier()
    {
        $meshQualifier = $this->container
            ->get('ilioscore.dataloader.meshqualifier')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshqualifiers',
                ['id' => $meshQualifier['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshqualifiers',
                ['id' => $meshQualifier['id']]
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
    public function testMeshQualifierNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshqualifiers', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
