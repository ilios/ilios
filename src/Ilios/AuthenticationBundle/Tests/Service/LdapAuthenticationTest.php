<?php
namespace Ilios\AuthenticationBundle\Tests\Service;

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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new LdapAuthentication(
            $authManager,
            $jwtManager,
            'host',
            'port',
            'bindTemplate'
        );
        
        $parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('get')->with('username')->andReturn(null)
            ->shouldReceive('get')->with('password')->andReturn(null)
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->request = $parameterBag;
        
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new LdapAuthentication(
            $authManager,
            $jwtManager,
            'host',
            'port',
            'bindTemplate'
        );
        
        $parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('get')->with('username')->andReturn('abc')
            ->shouldReceive('get')->with('password')->andReturn('123')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->request = $parameterBag;
        
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
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
        
        $parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('get')->with('username')->andReturn('abc')
            ->shouldReceive('get')->with('password')->andReturn('123')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->request = $parameterBag;
        
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
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

        $parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('get')->with('username')->andReturn('abc')
            ->shouldReceive('get')->with('password')->andReturn('123')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->request = $parameterBag;

        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
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
        
        $parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('get')->with('username')->andReturn('abc')
            ->shouldReceive('get')->with('password')->andReturn('123')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->request = $parameterBag;
        
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $newToken = m::mock('Ilios\AuthenticationBundle\Jwt\Token')
            ->shouldReceive('getJwt')->andReturn('jwt123Test')->mock();
        $jwtManager->shouldReceive('createJwtFromUser')->with($user)->andReturn('jwt123Test');

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
