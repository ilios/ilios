<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use App\Entity\UserInterface;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;
use App\Service\FormAuthentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class FormAuthenticationTest extends TestCase
{
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $userRepository;
    protected m\MockInterface $hasher;
    protected m\MockInterface $tokenStorage;
    protected m\MockInterface $jwtManager;
    protected m\MockInterface $sessionUserProvider;
    protected FormAuthentication $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->hasher = m::mock(UserPasswordHasherInterface::class);
        $this->tokenStorage = m::mock(TokenStorageInterface::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->userRepository = m::mock(UserRepository::class);
        $this->obj = new FormAuthentication(
            $this->authenticationRepository,
            $this->userRepository,
            $this->hasher,
            $this->tokenStorage,
            $this->jwtManager,
            $this->sessionUserProvider
        );
    }

    protected function tearDown(): void
    {
        unset($this->authenticationRepository);
        unset($this->userRepository);
        unset($this->hasher);
        unset($this->tokenStorage);
        unset($this->jwtManager);
        unset($this->sessionUserProvider);
        unset($this->obj);
    }

    public function testMissingValues(): void
    {
        $request = m::mock(Request::class);
        $arr = [
            'username' => null,
            'password' => null,
        ];
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
        $request = m::mock(Request::class);
        $arr = [
            'username' => 'abc',
            'password' => '123',
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn(null);
        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testBadPassword(): void
    {
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
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->hasher->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(false);
        $result = $this->obj->login($request);

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
        $this->authenticationRepository->shouldReceive('findOneByUsername')
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
        $this->hasher->shouldReceive('needsRehash')->with($sessionUser)->andReturn(false);
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->hasher->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(true);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
