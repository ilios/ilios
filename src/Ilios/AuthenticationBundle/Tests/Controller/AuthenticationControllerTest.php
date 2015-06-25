<?php

namespace Ilios\AuthenticationBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use JWT as TokenLib;

class AuthenticationControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    public function testMissingValues()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);

        $this->assertEquals(
            array('errors' => array('Username is required', 'Password is required')),
            json_decode($response->getContent(), true)
        );
    }

    public function testAuthenticateLegacyUser()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ));

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('jwt', $response));
        $token = (array) TokenLib::decode($response['jwt']);
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(1, $token['user_id']);
    }

    public function testAuthenticateUser()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'newuser',
            'password' => 'newuserpass'
        ));

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $response = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('jwt', $response));
        $token = (array) TokenLib::decode($response['jwt']);
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(2, $token['user_id']);
    }

    public function testWrongLegacyPassword()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'legacyuser',
            'password' => 'wronglegacyuserpass'
        ));

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);

        $response = json_decode($response->getContent(), true);

        $this->assertTrue(array_key_exists('errors', $response));
        $this->assertSame($response['errors'][0], "Incorrect username or password");
    }

    public function testWrongPassword()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'newuser',
            'password' => 'wrongnewuserpass'
        ));

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);

        $response = json_decode($response->getContent(), true);

        $this->assertTrue(array_key_exists('errors', $response));
        $this->assertSame($response['errors'][0], "Incorrect username or password");
    }
}
