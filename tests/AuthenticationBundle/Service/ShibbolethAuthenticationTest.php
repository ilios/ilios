<?php
namespace Tests\AuthenticationBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\ShibbolethAuthentication;

class ShibbolethAuthenticationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testConstructor()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
        );
        $this->assertTrue($obj instanceof ShibbolethAuthentication);
    }
    
    public function testNotAuthenticated()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');

        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
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
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');

        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
        );

        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Session-ID')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Authentication-Instant')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Authentication-Method')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Session-Index')->andReturn(true)
            ->shouldReceive('get')->with('HTTP_REFERER')->andReturn(true)
            ->shouldReceive('get')->with('REMOTE_ADDR')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn(false)
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        $logger->shouldReceive('error')->once();
        $this->expectException(\Exception::class);
        $obj->login($request);
    }
    
    public function testNoUserWithEppn()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');

        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
        );

        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;
        $authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn(null);

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'noAccountExists');
        $this->assertSame($data->userId, 'userid1');
    }
    
    public function testDisabledUser()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
        );
        
        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn($authenticationEntity);
        $jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');
        
        
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }

    public function testSuccess()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $logger = m::mock('Psr\Log\LoggerInterface');
        $obj = new ShibbolethAuthentication(
            $authManager,
            $jwtManager,
            $logger,
            '/Shibboleth.sso/Logout',
            'eppn'
        );

        $serverBag = m::mock('Symfony\Component\HttpFoundation\ServerBag')
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->server = $serverBag;

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();

        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn($authenticationEntity);
        $jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');


        $result = $obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
