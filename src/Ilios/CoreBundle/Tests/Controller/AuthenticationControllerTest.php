<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Authentication controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AuthenticationControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData'
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
    public function testPostAuthentication()
    {
        $data = $this->container->get('ilioscore.dataloader.authentication')
            ->create();

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentication' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAuthentication()
    {
        $invalidAuthentication = $this->container
            ->get('ilioscore.dataloader.authentication')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_authentications'),
            json_encode(['authentication' => $invalidAuthentication]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutAuthentication()
    {
        $existing = $this->container
            ->get('ilioscore.dataloader.authentication')
            ->getOne();

        $data = [
            'user' => $existing['user'],
            'username' => 'somethingnew',
            'password' => 'somethingnew'
        ];

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_authentications',
                ['userId' => $data['user']]
            ),
            json_encode(['authentication' => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
    }
}
