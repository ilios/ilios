<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * IlmSession controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class IlmSessionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
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
    public function testGetIlmSession()
    {
        $ilmSession = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_ilmsessions',
                ['id' => $ilmSession['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($ilmSession),
            json_decode($response->getContent(), true)['ilmSessions'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllIlmSessions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_ilmsessions'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.ilmsession')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['ilmSessions']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.ilmsession')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessions'),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['ilmSessions'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadIlmSession()
    {
        $invalidIlmSession = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_ilmsessions'),
            json_encode(['ilmSession' => $invalidIlmSession]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutIlmSession()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $data['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['ilmSession']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteIlmSession()
    {
        $ilmSession = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_ilmsessions',
                ['id' => $ilmSession['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_ilmsessions',
                ['id' => $ilmSession['id']]
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
    public function testIlmSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_ilmsessions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
