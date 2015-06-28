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

    public function testAuthenticateLegacyUserCaseInsensitve()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'LEGACYUSER',
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

    public function testAuthenticateUserCaseInsensitive()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'NEWUSER',
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

    public function testAuthenticatingLegacyUserChangesHash()
    {
        $client = static::createClient();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $legacyUser = $em->getRepository('IliosCoreBundle:User')->find(1);
        $authentication = $legacyUser->getAuthentication();
        $this->assertTrue($authentication->isLegacyAccount());
        $this->assertNotEmpty($authentication->getPasswordSha256());
        $this->assertEmpty($authentication->getPasswordBcrypt());


        $client->request('POST', '/auth/login', array(
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

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

        $legacyUser = $em->getRepository('IliosCoreBundle:User')->find(1);
        $authentication = $legacyUser->getAuthentication();
        $this->assertFalse($authentication->isLegacyAccount());
        $this->assertEmpty($authentication->getPasswordSha256());
        $this->assertNotEmpty($authentication->getPasswordBcrypt());
    }

    public function testWhoAmI()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', array(
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ));

        $response = $client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $response = json_decode($response->getContent(), true);
        $token = $response['jwt'];

        $client->request(
            'GET',
            '/auth/whoami',
            array(),
            array(),
            array('HTTP_X-JWT-Authorization' => 'Token ' . $token)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $response = json_decode($response->getContent(), true);

        $this->assertTrue(
            array_key_exists('userId', $response),
            'Response has user_id: ' . var_export($response, true)
        );
        $this->assertSame(
            $response['userId'],
            1,
            'Response has the correct user id: ' . var_export($response, true)
        );
    }
}
