<?php
namespace Tests\AuthenticationBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\LdapAuthentication;

class LdapAuthenticationTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    public function testConstructor()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new LdapAuthentication(
            $authManager,
            $jwtManager,
            'host',
            'port',
            'bindTemplate'
        );
        $this->assertTrue($obj instanceof LdapAuthentication);
    }
    
    public function testMissingValues()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new LdapAuthentication(
            $authManager,
            $jwtManager,
            'host',
            'port',
            'bindTemplate'
        );
        $arr = [
            'username' => null,
            'password' => null
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }
    
    public function testBadUserName()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new LdapAuthentication(
            $authManager,
            $jwtManager,
            'host',
            'port',
            'bindTemplate'
        );
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn(null);
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testBadPassword()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            'Ilios\AuthenticationBundle\Service\LdapAuthentication[checkLdapPassword]',
            array(
                $authManager,
                $jwtManager,
                'host',
                'port',
                'bindTemplate'
            )
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(false);
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        
        $result = $obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testDisabledUser()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            'Ilios\AuthenticationBundle\Service\LdapAuthentication[checkLdapPassword]',
            array(
                $authManager,
                $jwtManager,
                'host',
                'port',
                'bindTemplate'
            )
        );
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);

        $result = $obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testSuccess()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            'Ilios\AuthenticationBundle\Service\LdapAuthentication[checkLdapPassword]',
            array(
                $authManager,
                $jwtManager,
                'host',
                'port',
                'bindTemplate'
            )
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(true);
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $newToken = m::mock('Ilios\AuthenticationBundle\Jwt\Token')
            ->shouldReceive('getJwt')->andReturn('jwt123Test')->mock();
        $jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
