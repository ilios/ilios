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
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;
use App\Service\LdapAuthentication;
use Symfony\Component\HttpFoundation\Request;

class LdapAuthenticationTest extends TestCase
{
    protected m\MockInterface $authRepository;
    protected m\MockInterface $jwtManager;
    protected m\MockInterface $sessionUserProvider;
    protected m\MockInterface $config;
    protected LdapAuthentication $obj;

    public function setUp(): void
    {
        parent::setUp();
        $this->authRepository = m::mock(AuthenticationRepository::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')->with('ldap_authentication_host')->andReturn('host');
        $this->config->shouldReceive('get')->with('ldap_authentication_port')->andReturn('port');
        $this->config->shouldReceive('get')->with('ldap_authentication_bind_template')->andReturn('bindTemplate');
        $this->obj = new LdapAuthentication(
            $this->authRepository,
            $this->jwtManager,
            $this->config,
            $this->sessionUserProvider
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->authRepository);
        unset($this->jwtManager);
        unset($this->sessionUserProvider);
        unset($this->config);
    }

    public function testMissingValues(): void
    {
        $arr = [
            'username' => null,
            'password' => null,
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }

    public function testBadUserName(): void
    {
        $arr = [
            'username' => 'abc',
            'password' => '123',
        ];
        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $this->authRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn(null);
        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testBadPassword(): void
    {
        $obj = m::mock(
            LdapAuthentication::class . '[checkLdapPassword]',
            [
                $this->authRepository,
                $this->jwtManager,
                $this->config,
                $this->sessionUserProvider,
            ]
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(false);
        $arr = [
            'username' => 'abc',
            'password' => '123',
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(true);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $result = $obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testDisabledUser(): void
    {
        $arr = [
            'username' => 'abc',
            'password' => '123',
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(false);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);


        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testSuccess(): void
    {
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            LdapAuthentication::class . '[checkLdapPassword]',
            [
                $this->authRepository,
                $this->jwtManager,
                $this->config,
                $this->sessionUserProvider,
            ]
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(true);
        $arr = [
            'username' => 'abc',
            'password' => '123',
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(true);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
