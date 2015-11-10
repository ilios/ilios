<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class BadRequestControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->loadFixtures([
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    /**
     * @group controllers
     */
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ));

        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token = $response['jwt'];

        $client->request(
            'GET',
            '/api/nothing',
            array(),
            array(),
            array('HTTP_X-JWT-Authorization' => 'Token ' . $token)
        );
        $response = $client->getResponse();
        $this->assertEquals(
            404,
            $response->getStatusCode()
        );

    }
}
