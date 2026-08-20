<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\FormAuthentication;
use App\Service\Jwt\TokenCodec;
use App\Service\Jwt\TokenFactory;
use App\Service\Jwt\TokenManager;
use App\Service\SecretManager;
use App\Service\SessionUserPermissionChecker;
use App\Service\SessionUserProvider;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class FormAuthenticationTest extends TestCase
{
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $userRepository;
    protected m\MockInterface $hasher;
    protected m\MockInterface $tokenStorage;
    protected m\MockInterface $sessionUserPermissionChecker;
    protected m\MockInterface $sessionUserProvider;
    protected TokenCodec $tokenCodec;
    protected TokenManager $tokenManager;
    protected FormAuthentication $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->hasher = m::mock(UserPasswordHasherInterface::class);
        $this->tokenStorage = m::mock(TokenStorageInterface::class);
        $this->tokenCodec = new TokenCodec(new SecretManager('FFFFFFFFDDDDDDDDDDAAAAAAB', ''));
        $this->sessionUserPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->tokenManager = new TokenManager(
            $this->tokenCodec,
            new TokenFactory(),
            $this->sessionUserPermissionChecker
        );
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->userRepository = m::mock(UserRepository::class);
        $this->obj = new FormAuthentication(
            $this->authenticationRepository,
            $this->userRepository,
            $this->hasher,
            $this->tokenStorage,
            $this->tokenCodec,
            $this->tokenManager,
            $this->sessionUserProvider
        );
    }

    protected function tearDown(): void
    {
        unset($this->authenticationRepository);
        unset($this->userRepository);
        unset($this->hasher);
        unset($this->tokenStorage);
        unset($this->tokenManager);
        unset($this->tokenCodec);
        unset($this->sessionUserPermissionChecker);
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

    #[DataProvider('successProvider')]
    public function testSuccess(int $userId, bool $isRoot, bool $performsNonLearnerFunction, bool $canCreateUsers): void
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
        $sessionUser->shouldReceive('getId')->andReturn($userId);
        $sessionUser->shouldReceive('isRoot')->andReturn($isRoot);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->hasher->shouldReceive('needsRehash')->with($sessionUser)->andReturn(false);
        $this->authenticationRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->hasher->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(true);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->andReturn($canCreateUsers);

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);

        $this->assertSame($data->status, 'success');

        $tokenData = $this->tokenCodec->decode($data->jwt);

        $this->assertCount(10, $tokenData);
        $this->assertSame(
            DateTimeImmutable::createFromFormat('U', $tokenData['iat'])
                ->add(new DateInterval(TokenManager::USER_TOKEN_DEFAULT_TTL))
                ->getTimestamp(),
            DateTimeImmutable::createFromFormat('U', $tokenData['exp'])->getTimestamp()
        );
        $this->assertSame(TokenManager::TOKEN_DEFAULT_ISSUER, $tokenData['iss']);
        $this->assertSame(TokenManager::TOKEN_DEFAULT_AUDIENCE, $tokenData['aud']);
        $this->assertSame($userId, $tokenData['user_id']);
        $this->assertSame($isRoot, $tokenData['is_root']);
        $this->assertSame($performsNonLearnerFunction, $tokenData['performs_non_learner_function']);
        $this->assertSame($canCreateUsers, $tokenData['can_create_or_update_user_in_any_school']);
        $this->assertSame($tokenData['firstCreatedAt'], $tokenData['iat']);
        $this->assertSame(0, $tokenData['refreshCount']);
    }

    public static function successProvider(): array
    {
        return [
            [10, true, false, true],
            [20, false, true, false],
        ];
    }
}
