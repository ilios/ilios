<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Service\Config;
use App\Service\Jwt\TokenCodec;
use App\Service\Jwt\TokenFactory;
use App\Service\Jwt\TokenManager;
use App\Service\LdapAuthentication;
use App\Service\SecretManager;
use App\Service\SessionUserPermissionChecker;
use App\Service\SessionUserProvider;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

final class LdapAuthenticationTest extends TestCase
{
    protected m\MockInterface $authRepository;
    protected m\MockInterface $sessionUserProvider;
    protected m\MockInterface $sessionUserPermissionChecker;
    protected m\MockInterface $config;
    protected TokenCodec $tokenCodec;
    protected TokenManager $tokenManager;
    protected LdapAuthentication $obj;

    public function setUp(): void
    {
        parent::setUp();
        $this->authRepository = m::mock(AuthenticationRepository::class);
        $this->tokenCodec = new TokenCodec(new SecretManager('FFFFFFFFDDDDDDDDDDAAAAAAB', ''));
        $this->sessionUserPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->tokenManager = new TokenManager(
            new TokenFactory(),
            $this->sessionUserPermissionChecker
        );
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')->with('ldap_authentication_host')->andReturn('host');
        $this->config->shouldReceive('get')->with('ldap_authentication_port')->andReturn('port');
        $this->config->shouldReceive('get')->with('ldap_authentication_bind_template')->andReturn('bindTemplate');
        $this->obj = new LdapAuthentication(
            $this->authRepository,
            $this->tokenCodec,
            $this->tokenManager,
            $this->config,
            $this->sessionUserProvider
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->authRepository);
        unset($this->tokenManager);
        unset($this->tokenCodec);
        unset($this->sessionUserPermissionChecker);
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
                $this->tokenCodec,
                $this->tokenManager,
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

    #[DataProvider('successProvider')]
    public function testSuccess(int $userId, bool $isRoot, bool $performsNonLearnerFunction, bool $canCreateUsers): void
    {
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            LdapAuthentication::class . '[checkLdapPassword]',
            [
                $this->authRepository,
                $this->tokenCodec,
                $this->tokenManager,
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
        $sessionUser->shouldReceive('getId')->andReturn($userId);
        $sessionUser->shouldReceive('isRoot')->andReturn($isRoot);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->andReturn($canCreateUsers);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authRepository->shouldReceive('findOneByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $result = $obj->login($request);

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
