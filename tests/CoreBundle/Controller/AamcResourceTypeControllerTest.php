<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AamcResourceType controller Test.
 * @package Ilios\CoreBundle\Test\Controller
 */
class AamcResourceTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadAamcResourceTypeData',
            'Tests\CoreBundle\Fixture\LoadTermData'
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
    public function testGetAamcResourceType()
    {
        $aamcResourceType = $this->container
            ->get('ilioscore.dataloader.aamcresourcetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcresourcetypes',
                ['id' => $aamcResourceType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($aamcResourceType),
            json_decode($response->getContent(), true)['aamcResourceTypes'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAamcResourceType()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_aamcresourcetypes'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.aamcresourcetype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['aamcResourceTypes']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAamcResourceType()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcresourcetype')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcresourcetypes'),
            json_encode(['aamcResourceTypes' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['aamcResourceTypes'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostTermAamcResourceType()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcresourcetype')->create();
        $postData = $data;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcresourcetypes'),
            json_encode(['aamcResourceTypes' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['aamcResourceTypes'][0]['id'];
        foreach ($postData['terms'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_terms',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['terms'][0];
            $this->assertTrue(in_array($newId, $data['aamcResourceTypes']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAamcResourceType()
    {
        $invalidAamcResourceType = $this->container
            ->get('ilioscore.dataloader.aamcresourcetype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcresourcetypes'),
            json_encode(['aamcResourceTypes' => $invalidAamcResourceType]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutAamcResourceType()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.aamcresourcetype')
            ->getOne();
        $data['terms'] = ['3'];

        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcresourcetypes',
                ['id' => $data['id']]
            ),
            json_encode(['aamcResourceTypes' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['aamcResourceType']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteAamcResourceType()
    {
        $aamcResourceType = $this->container
            ->get('ilioscore.dataloader.aamcresourcetype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_aamcresourcetypes',
                ['id' => $aamcResourceType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcresourcetypes',
                ['id' => $aamcResourceType['id']]
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
    public function testAamcResourceTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_aamcresourcetypes', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
