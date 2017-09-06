<?php
namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Service\Config;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\ShibbolethAuthentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;

class ShibbolethAuthenticationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $authManager;
    protected $jwtManager;
    protected $logger;
    protected $config;
    protected $obj;

    public function setup()
    {
        $this->authManager = m::mock(AuthenticationManager::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->logger = m::mock(LoggerInterface::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')->with('shibboleth_authentication_logout_path')
            ->andReturn('/Shibboleth.sso/Logout');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_login_path')
            ->andReturn('/Shibboleth.sso/Login');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_user_id_attribute')->andReturn('eppn');
        $this->obj = new ShibbolethAuthentication(
            $this->authManager,
            $this->jwtManager,
            $this->logger,
            $this->config
        );
    }

    public function tearDown()
    {
        unset($this->obj);
        unset($this->authManager);
        unset($this->jwtManager);
        unset($this->logger);
        unset($this->config);
    }


    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof ShibbolethAuthentication);
    }
    
    public function testNotAuthenticated()
    {
        $serverBag = m::mock(ServerBag::class)
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(false)
            ->mock();
        $request = m::mock(Request::class);
        $request->server = $serverBag;
        
        $result = $this->obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }
    
    public function testNoEppn()
    {
        $serverBag = m::mock(ServerBag::class)
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Session-ID')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Authentication-Instant')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Authentication-Method')->andReturn(true)
            ->shouldReceive('get')->with('Shib-Session-Index')->andReturn(true)
            ->shouldReceive('get')->with('HTTP_REFERER')->andReturn(true)
            ->shouldReceive('get')->with('REMOTE_ADDR')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn(false)
            ->mock();
        $request = m::mock(Request::class);
        $request->server = $serverBag;
        $this->logger->shouldReceive('info')->once();

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }
    
    public function testNoUserWithEppn()
    {
        $serverBag = m::mock(ServerBag::class)
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock(Request::class);
        $request->server = $serverBag;
        $this->authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn(null);

        $result = $this->obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'noAccountExists');
        $this->assertSame($data->userId, 'userid1');
    }
    
    public function testDisabledUser()
    {
        $serverBag = m::mock(ServerBag::class)
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $this->authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn($authenticationEntity);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');
        
        
        $result = $this->obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }

    public function testSuccess()
    {
        $serverBag = m::mock(ServerBag::class)
            ->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true)
            ->shouldReceive('get')->with('eppn')->andReturn('userid1')
            ->mock();
        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();

        $authenticationEntity = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $this->authManager->shouldReceive('findOneBy')
            ->with(array('username' => 'userid1'))->andReturn($authenticationEntity);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');


        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
