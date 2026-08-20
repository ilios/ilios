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
use App\Service\SecretManager;
use App\Service\SessionUserPermissionChecker;
use App\Service\SessionUserProvider;
use App\Service\ShibbolethAuthentication;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShibbolethAuthenticationTest extends TestCase
{
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $jwtManager;
    protected m\MockInterface $logger;
    protected m\MockInterface $config;
    protected m\MockInterface $sessionUserPermissionChecker;
    protected m\MockInterface $sessionUserProvider;
    protected TokenCodec $tokenCodec;
    protected TokenManager $tokenManager;
    protected ShibbolethAuthentication $obj;

    public function setUp(): void
    {
        parent::setUp();
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->logger = m::mock(LoggerInterface::class);
        $this->config = m::mock(Config::class);
        $this->tokenCodec = new TokenCodec(new SecretManager('FFFFFFFFDDDDDDDDDDAAAAAAB', ''));
        $this->sessionUserPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->tokenManager = new TokenManager(
            new TokenFactory(),
            $this->sessionUserPermissionChecker
        );
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->config->shouldReceive('get')->with('shibboleth_authentication_logout_path')
            ->andReturn('/Shibboleth.sso/Logout');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_login_path')
            ->andReturn('/Shibboleth.sso/Login');
        $this->config->shouldReceive('get')->with('shibboleth_authentication_user_id_attribute')->andReturn('eppn');
        $this->obj = new ShibbolethAuthentication(
            $this->authenticationRepository,
            $this->tokenCodec,
            $this->tokenManager,
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
        unset($this->logger);
        unset($this->config);
        unset($this->tokenManager);
        unset($this->tokenCodec);
        unset($this->sessionUserPermissionChecker);
        unset($this->sessionUserProvider);
    }

    public function testNotAuthenticated(): void
    {
        $request = new Request(server: ['Shib-Application-ID' => false]);
        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }

    public function testNoEppn(): void
    {
        $request = new Request(server: [
            'Shib-Application-ID' => true,
            'Shib-Session-ID' => true,
            'Shib-Authentication-Instant' => true,
            'Shib-Authentication-Method' => true,
            'Shib-Session-Index' => true,
            'HTTP_REFERER' => true,
            'REMOTE_ADDRESS' => false,
            'eppn' => false,
        ]);

        $this->logger->shouldReceive('info')->once();

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'redirect');
    }

    public function testNoUserWithEppn(): void
    {
        $request = new Request(server: ['Shib-Application-ID' => true, 'eppn' => 'userid1']);
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
        $request = new Request(server: ['Shib-Application-ID' => true, 'eppn' => 'userid1']);
        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(false);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'userid1'])->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $result = $this->obj->login($request);

        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame('noAccountExists', $data->status);
        $this->assertSame('userid1', $data->userId);
    }

    #[DataProvider('successProvider')]
    public function testSuccess(int $userId, bool $isRoot, bool $performsNonLearnerFunction, bool $canCreateUsers): void
    {
        $request = new Request(server: ['Shib-Application-ID' => true, 'eppn' => 'userid1']);
        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isEnabled')->andReturn(true);
        $sessionUser->shouldReceive('getId')->andReturn($userId);
        $sessionUser->shouldReceive('isRoot')->andReturn($isRoot);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $authenticationEntity = m::mock(AuthenticationInterface::class);
        $authenticationEntity->shouldReceive('getUser')->andReturn($user);
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'userid1'])->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
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

    public function testCreateAuthenticationResponseAuthenticated(): void
    {
        $request = new Request(server: ['Shib-Application-ID' => true]);
        $result = $this->obj->createAuthenticationResponse($request);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertNotInstanceOf(RedirectResponse::class, $result);
    }

    public function testCreateAuthenticationResponseNotAuthenticated(): void
    {
        $request = new Request(server: [
            'HTTPS' => false,
            'SERVER_NAME' => 'testhost',
            'REQUEST_URI' => 'something.html',
        ]);

        $result = $this->obj->createAuthenticationResponse($request);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertStringContainsString('?target=something.html', $result->getTargetUrl());
    }
}
