<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Service\Config;
use App\Tests\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;
use App\Service\ShibbolethAuthentication;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;

class ShibbolethAuthenticationTest extends TestCase
{
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $jwtManager;
    protected m\MockInterface $logger;
    protected m\MockInterface $config;
    protected ShibbolethAuthentication $obj;
    protected m\MockInterface $sessionUserProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->logger = m::mock(LoggerInterface::class);
        $this->config = m::mock(Config::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->config->shouldReceive('get')->with('shibboleth_authentication_logout_path')
            ->andReturn('/Shibboleth.sso/Logout');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_login_path')
            ->andReturn('/Shibboleth.sso/Login');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_user_id_attribute')->andReturn('eppn');
        $this->obj = new ShibbolethAuthentication(
            $this->authenticationRepository,
            $this->jwtManager,
            $this->logger,
            $this->config,
            $this->sessionUserProvider
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->authenticationRepository);
        unset($this->jwtManager);
        unset($this->logger);
        unset($this->config);
        unset($this->sessionUserProvider);
    }

    public function testNotAuthenticated(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(false);

        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }

    public function testNoEppn(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true);
        $serverBag->shouldReceive('get')->with('Shib-Session-ID')->andReturn(true);
        $serverBag->shouldReceive('get')->with('Shib-Authentication-Instant')->andReturn(true);
        $serverBag->shouldReceive('get')->with('Shib-Authentication-Method')->andReturn(true);
        $serverBag->shouldReceive('get')->with('Shib-Session-Index')->andReturn(true);
        $serverBag->shouldReceive('get')->with('HTTP_REFERER')->andReturn(true);
        $serverBag->shouldReceive('get')->with('REMOTE_ADDR')->andReturn(true);
        $serverBag->shouldReceive('get')->with('eppn')->andReturn(false);

        $request = m::mock(Request::class);
        $request->server = $serverBag;
        $this->logger->shouldReceive('info')->once();

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }

    public function testNoUserWithEppn(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true);
        $serverBag->shouldReceive('get')->with('eppn')->andReturn('userid1');

        $request = m::mock(Request::class);
        $request->server = $serverBag;
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'userid1'])->andReturn(null);

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'noAccountExists');
        $this->assertSame($data->userId, 'userid1');
    }

    public function testDisabledUser(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true);
        $serverBag->shouldReceive('get')->with('eppn')->andReturn('userid1');

        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(true);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'userid1'])->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }

    public function testSuccess(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true);
        $serverBag->shouldReceive('get')->with('eppn')->andReturn('userid1');

        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(true);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'userid1'])->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }

    public function testCreateAuthenticationResponseAuthenticated(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(true);

        $request = m::mock(Request::class);
        $request->server = $serverBag;

        $result = $this->obj->createAuthenticationResponse($request);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertNotInstanceOf(RedirectResponse::class, $result);
    }

    public function testCreateAuthenticationResponseNotAuthenticated(): void
    {
        $serverBag = m::mock(ServerBag::class);
        $serverBag->shouldReceive('get')->with('Shib-Application-ID')->andReturn(false);

        $request = m::mock(Request::class);
        $request->shouldReceive('getSchemeAndHttpHost')->andReturn('http://testhost');
        $request->shouldReceive('getRequestUri')->andReturn('something.html');

        $request->server = $serverBag;

        $result = $this->obj->createAuthenticationResponse($request);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertStringContainsString('?target=something.html', $result->getTargetUrl());
    }
}
