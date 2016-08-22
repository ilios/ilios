<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AamcMethod controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AamcMethodControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData'
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
     * @group controllers_a_a
     */
    public function testGetAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($aamcMethod),
            json_decode($response->getContent(), true)['aamcMethods'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAamcMethods()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_aamcmethods'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.aamcmethod')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['aamcMethods']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostAamcMethod()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcmethod')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(['aamcMethod' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['aamcMethods'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostSessionTypeAamcMethod()
    {
        $data = $this->container->get('ilioscore.dataloader.aamcmethod')->create();
        $postData = $data;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(['aamcMethod' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['aamcMethods'][0]['id'];
        foreach ($postData['sessionTypes'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_sessiontypes',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['sessionTypes'][0];
            $this->assertTrue(in_array($newId, $data['aamcMethods']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAamcMethod()
    {
        $invalidAamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_aamcmethods'),
            json_encode(['aamcMethod' => $invalidAamcMethod]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutAamcMethod()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_aamcmethods',
                ['id' => $data['id']]
            ),
            json_encode(['aamcMethod' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['aamcMethod']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteAamcMethod()
    {
        $aamcMethod = $this->container
            ->get('ilioscore.dataloader.aamcmethod')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_aamcmethods',
                ['id' => $aamcMethod['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @group controllers
     */
    public function testAamcMethodNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_aamcmethods', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
