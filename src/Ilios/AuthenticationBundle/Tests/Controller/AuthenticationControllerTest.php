<?php

namespace Ilios\AuthenticationBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use JWT as TokenLib;
use Ilios\AuthenticationBundle\Jwt\Token;

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
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
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
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        
        $token = (array) TokenLib::decode($data->jwt);
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
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        
        $token = (array) TokenLib::decode($data->jwt);
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
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        
        $token = (array) TokenLib::decode($data->jwt);
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
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        
        $token = (array) TokenLib::decode($data->jwt);
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
        
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
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
        $content = $response->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertTrue(property_exists($data, 'jwt'));
        
        $token = (array) TokenLib::decode($data->jwt);
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
    
    public function testRefreshToken()
    {
        $client = static::createClient();
    
        // log in, grab token
        $client->request('POST', '/auth/login', array(
            'username' => 'newuser',
            'password' => 'newuserpass'
        ));
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token = (array) TokenLib::decode($response['jwt']);
    
        // send refresh token request
        // grab new token
        $client->request(
            'GET',
            '/auth/refresh',
            array(),
            array(),
            array('HTTP_X-JWT-Authorization' => 'Token ' . $response['jwt'])
        );
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token2 = (array) TokenLib::decode($response['jwt']);
    
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
    
    public function testRefreshTokenWithNonDefaultTtl()
    {
        $client = static::createClient();
    
        // log in, grab token
        $client->request('POST', '/auth/login', array(
            'username' => 'newuser',
            'password' => 'newuserpass'
        ));
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token = (array) TokenLib::decode($response['jwt']);
    
        // we set a non-default issued and expiration date with a delta of 42 days.
        $interval = new \DateInterval('P42D');
        $iat = new \DateTime();
        $exp = new \DateTime();
        $exp->setTimestamp($iat->getTimestamp());
        $exp->add($interval);
        $token['iat'] = $iat->format('U');
        $token['exp'] = $exp->format('U');
    
        // here, it is necessary to get interval from created datetimes again
        // b/c of date segment overflow shenanigans.
        // see http://php.net/manual/en/dateinterval.format.php#refsect1-dateinterval.format-notes
        $interval = $iat->diff($exp);
    
        // re-encode the token
        $key = Token::PREPEND_KEY . static::$kernel->getContainer()->getParameter('kernel.secret');
        $token = TokenLib::encode($token, $key);
    
        // send refresh token request
        // grab new token
        $client->request(
            'GET',
            '/auth/refresh',
            array(),
            array(),
            array('HTTP_X-JWT-Authorization' => 'Token ' . $token)
        );
        $response = $client->getResponse();
        $response = json_decode($response->getContent(), true);
        $token2 = (array) TokenLib::decode($response['jwt']);
    
        // get date diff on new token
        $exp2 = new \DateTime();
        $exp2->setTimestamp($token2['exp']);
        $iat2 = new \DateTime();
        $iat2->setTimestamp($token2['iat']);
        $interval2 = $iat2->diff($exp2);
    
        // should be the same
        $this->assertSame($interval->format('%R%Y/%M/%D %H:%I:%S'), $interval2->format('%R%Y/%M/%D %H:%I:%S'));
    }
}
