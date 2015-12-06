<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * MeshTree controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshTreeControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshTreeData',
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
    public function testGetMeshTree()
    {
        $meshTree = $this->container
            ->get('ilioscore.dataloader.meshtree')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshtrees',
                ['id' => $meshTree['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshTrees'][0];
        $this->assertEquals(
            $this->mockSerialize($meshTree),
            $data
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllMeshTrees()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshtrees'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshTrees'];
        $now = new DateTime();
        $this->assertEquals(
            $this->mockSerialize(
                $this->container->get('ilioscore.dataloader.meshtree')->getAll()
            ),
            $responses
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostMeshTree()
    {
        $data = $this->container->get('ilioscore.dataloader.meshTree')
            ->create();
        $postData = $data;
        unset($postData['id']);
        
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshtrees'),
            json_encode(['meshTree' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        
        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshTrees', $response), var_export($response, true));
        $data = $response['meshTrees'][0];
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );

    }

    /**
     * @group controllers_b
     */
    public function testPostBadMeshTree()
    {
        $invalidMeshTree = $this->container
            ->get('ilioscore.dataloader.meshtree')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshtrees'),
            json_encode(['meshTree' => $invalidMeshTree]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutMeshTree()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.meshtree')
            ->getOne();
        $postData['name'] = 'somethign new';
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['concepts']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshtrees',
                ['id' => $data['id']]
            ),
            json_encode(['meshTree' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshTree'];
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteMeshTree()
    {
        $meshTree = $this->container
            ->get('ilioscore.dataloader.meshtree')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshtrees',
                ['id' => $meshTree['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshtrees',
                ['id' => $meshTree['id']]
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
    public function testMeshTreeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshtrees', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
