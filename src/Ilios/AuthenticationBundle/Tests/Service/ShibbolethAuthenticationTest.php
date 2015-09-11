<?php
namespace Ilios\AuthenticationBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\ShibbolethAuthentication;

class ShibbolethAuthenticationTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        $this->assertTrue($obj instanceof ShibbolethAuthentication);
    }
    
    public function testNotAuthenticated()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(false)
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }
    
    public function testNoEppn()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn(false)
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        $this->setExpectedException('Exception');
        $obj->login($request);
    }
    
    public function testNoUserWithEppn()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        $authManager->shouldReceive('findAuthenticationBy')
            ->with(array('eppn' => 'userid1'))->andReturn(null);

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'noAccountExists');
        $this->assertSame($data->eppn, 'userid1');
    }
    
    public function testSuccess()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface');
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $authManager->shouldReceive('findAuthenticationBy')
            ->with(array('eppn' => 'userid1'))->andReturn($authenticationEntity);
        $jwtManager->shouldReceive('createJwtFromUser')->with($user)->andReturn('jwt123Test');
        
        
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
    
    public function testAddNewUser()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface');
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')->with($user)->once()
            ->shouldReceive('setEppn')->with('abc123')->once()
            ->mock();
        $user->shouldReceive('setAuthentication')->with($authenticationEntity)->once();
        $authManager
            ->shouldReceive('createAuthentication')->andReturn($authenticationEntity)->once()
            ->shouldReceive('updateAuthentication')->with($authenticationEntity, false)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'eppn' => 'abc123'
        ];
        
        $obj->setupNewUser($fakeDirectoryUser, $user);
    }
    
    public function testSyncUser()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager
        );
        
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface');
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setEppn')->with('abc123')->once()
            ->mock();
        $user->shouldReceive('getAuthentication')->andReturn($authenticationEntity)->once();
        $authManager->shouldReceive('updateAuthentication')->with($authenticationEntity, false)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'eppn' => 'abc123'
        ];
        
        $obj->syncUser($fakeDirectoryUser, $user);
    }
}
