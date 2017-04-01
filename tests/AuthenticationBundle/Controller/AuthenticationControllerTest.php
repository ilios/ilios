<?php

namespace Tests\AuthenticationBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Firebase\JWT\JWT;
use DateTime;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;

class AuthenticationControllerTest extends WebTestCase
{
    use JsonControllerTest;

    /**
     * @var string
     */
    protected $jwtKey;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadAuthenticationData'
        ]);

        $this->jwtKey = JsonWebTokenManager::PREPEND_KEY . $this->getContainer()->getParameter('kernel.secret');
    }

    public function testMissingValues()
    {
        $client = static::createClient();
        $client->request('POST', '/auth/login');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }

    public function testAuthenticateLegacyUser()
    {
        $client = static::createClient();
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ]));
    
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        $token = (array) JWT::decode($data->jwt, $this->jwtKey, array('HS256'));
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(1, $token['user_id']);
    }
    
    public function testAuthenticateUser()
    {
        $client = static::createClient();

        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'newuser',
            'password' => 'newuserpass'
        ]));
    
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_OK);
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = (array) JWT::decode($data->jwt, $this->jwtKey, array('HS256'));
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(2, $token['user_id']);
    }
    
    public function testAuthenticateLegacyUserCaseInsensitve()
    {
        $client = static::createClient();
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'LEGACYUSER',
            'password' => 'legacyuserpass'
        ]));
    
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = (array) JWT::decode($data->jwt, $this->jwtKey, array('HS256'));
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(1, $token['user_id']);
    }
    
    public function testAuthenticateUserCaseInsensitive()
    {
        $client = static::createClient();
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'NEWUSER',
            'password' => 'newuserpass'
        ]));
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = (array) JWT::decode($data->jwt, $this->jwtKey, array('HS256'));
        $this->assertTrue(array_key_exists('user_id', $token));
        $this->assertSame(2, $token['user_id']);
    }
    
    public function testWrongLegacyPassword()
    {
        $client = static::createClient();
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'legacyuser',
            'password' => 'wronglegacyuserpass'
        ]));
    
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testWrongPassword()
    {
        $client = static::createClient();
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'newuser',
            'password' => 'wrongnewuserpass'
        ]));
    
        $response = $client->getResponse();
    
        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
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
    
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ]));
    
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
    
        $client->request('POST', '/auth/login', [], [], [], json_encode([
            'username' => 'legacyuser',
            'password' => 'legacyuserpass'
        ]));
    
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));

        $token = (array) JWT::decode($data->jwt, $this->jwtKey, array('HS256'));
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
        $jwt = $this->getAuthenticatedUserToken();
        $this->makeJsonRequest(
            $client,
            'get',
            $this->getUrl('ilios_authentication.whoami'),
            null,
            $jwt
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $response = json_decode($response->getContent(), true);
    
        $this->assertTrue(
            array_key_exists('userId', $response),
            'Response has user_id: ' . var_export($response, true)
        );
        $this->assertSame(
            $response['userId'],
            2,
            'Response has the correct user id: ' . var_export($response, true)
        );
    }
    
    public function testGetToken()
    {
        $client = static::createClient();
        $jwt = $this->getAuthenticatedUserToken();
        $token = (array) JWT::decode($jwt, $this->jwtKey, array('HS256'));
        $this->makeJsonRequest(
            $client,
            'get',
            $this->getUrl('ilios_authentication.token'),
            null,
            $jwt
        );
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token2 = (array) JWT::decode($response['jwt'], $this->jwtKey, array('HS256'));
    
        // figure out the delta between issued and expiration datetime
        $exp = new \DateTime();
        $exp->setTimestamp($token['exp']);
        $iat = new \DateTime();
        $iat->setTimestamp($token['iat']);
        $interval = $iat->diff($exp);
    
        // do it again for the new token
        $exp2 = new \DateTime();
        $exp2->setTimestamp($token2['exp']);
        $iat2 = new \DateTime();
        $iat2->setTimestamp($token2['iat']);
        $interval2 = $iat2->diff($exp2);
    
        // test for sameness
        $this->assertSame($token['user_id'], $token2['user_id']);
        $this->assertSame($token['iss'], $token2['iss']);
        $this->assertSame($token['aud'], $token2['aud']);
        // http://php.net/manual/en/dateinterval.format.php
        $this->assertSame($interval->format('%R%Y/%M/%D %H:%I:%S'), $interval2->format('%R%Y/%M/%D %H:%I:%S'));
    }
    
    public function testGetTokenWithNonDefaultTtl()
    {
        $client = static::createClient();
        $jwt = $this->getAuthenticatedUserToken();
        $this->makeJsonRequest(
            $client,
            'get',
            $this->getUrl('ilios_authentication.token') . '?ttl=P2W',
            [],
            $jwt
        );
        
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token = (array) JWT::decode($response['jwt'], $this->jwtKey, array('HS256'));
        
        
        $now = new DateTime();
        $expiresAt = new DateTime();
        $expiresAt->setTimeStamp($token['exp']);
        
        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }

    public function testInvalidateToken()
    {
        $client = static::createClient();
        $jwt = $this->getAuthenticatedUserToken();
        sleep(1);

        $this->makeJsonRequest(
            $client,
            'get',
            $this->getUrl('ilios_authentication.invalidate_tokens'),
            null,
            $jwt
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $client = static::createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                'ilios_api_get',
                ['object' => 'users', 'version' => 'v1', 'id' => 1]
            ),
            null,
            $jwt
        );
        $response2 = $client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response2->getStatusCode());
        $this->assertRegExp('/Invalid JSON Web Token: Not issued after/', $response2->getContent());
    }
}
