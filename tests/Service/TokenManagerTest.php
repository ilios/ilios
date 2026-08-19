<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\ServiceToken;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use App\Service\SecretManager;
use App\Service\SessionUserPermissionChecker;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Mockery as m;

#[CoversClass(TokenManager::class)]
final class TokenManagerTest extends TestCase
{
    protected m\MockInterface $sessionUserPermissionChecker;
    protected TokenCodec $codec;
    protected TokenFactory $factory;
    protected TokenManager $manager;

    public function setUp(): void
    {
        parent::setUp();
        $this->sessionUserPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->codec = new TokenCodec(new SecretManager('TheSignOfTheSouthernCross', 'ChildrenOfTheSea'));
        $this->factory = new TokenFactory();
        $this->manager = new TokenManager(
            $this->codec,
            $this->factory,
            $this->sessionUserPermissionChecker
        );
    }

    public function tearDown(): void
    {
        unset($this->sessionUserPermissionChecker);
        unset($this->manager);
        unset($this->codec);
        unset($this->factory);
        parent::tearDown();
    }

    #[DataProvider('extractFromJwtProvider')]
    public function testExtractServiceTokenFromJwt(array $input, string $expectedTokenType): void
    {
        $jwt = $this->codec->encode($input);
        $token = $this->manager->extractJwt($jwt);
        $this->assertInstanceOf($expectedTokenType, $token);
    }

    public static function extractFromJwtProvider(): array
    {
        return [
            [
                [
                    'iat' => new DateTimeImmutable()->format('U'),
                    'exp' => new DateTimeImmutable()->add(new DateInterval('PT8H'))->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'user_id' => 1,
                ],
                UserToken::class,
            ],
            [
                [
                    'iat' => new DateTimeImmutable()->format('U'),
                    'exp' => new DateTimeImmutable()->add(new DateInterval('PT8H'))->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'token_id' => 1,
                ],
                ServiceToken::class,
            ],
        ];
    }

    #[DataProvider('createUserTokenFromSessionUserProvider')]
    public function testCreateUserTokenFromSessionUser(
        int $userId,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        string $ttl,
    ): void {
        $sessionUserMock = m::mock(SessionUserInterface::class);
        $sessionUserMock->shouldReceive('getId')->andReturn($userId);
        $sessionUserMock->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $sessionUserMock->shouldReceive('isRoot')->andReturn($isRoot);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUserMock)
            ->andReturn(true);
        $token = $this->manager->createUserTokenForSessionUser($sessionUserMock, $ttl);

        $this->assertSame(
            $token->expiresAt->getTimestamp(),
            $token->issuedAt->add(new DateInterval($ttl))->getTimestamp()
        );
        $this->assertSame($userId, $token->userId);
        $this->assertSame('ilios', $token->issuer);
        $this->assertSame(['ilios'], $token->audience);
        $this->assertSame($token->firstCreatedAt->getTimestamp(), $token->issuedAt->getTimestamp());
        $this->assertSame($isRoot, $token->isRoot);
        $this->assertSame($performsNonLearnerFunction, $token->performsNonLearnerFunction);
        $this->assertSame(0, $token->refreshCount);
    }

    public static function createUserTokenFromSessionUserProvider(): array
    {
        return [
            [10, false, true, 'PT8H'],
            [20, true, false, 'P30D'],
        ];
    }

    public function testCreateUserTokenFromSessionWithTtlExceedingMaximum(): void
    {
        $ttl = 'P91D';
        $ttlInterval = new DateInterval($ttl);
        $maxTtlInterval = new DateInterval(TokenManager::USER_TOKEN_MAX_TTL);
        assert($maxTtlInterval > $ttlInterval);
        $sessionUserMock = m::mock(SessionUserInterface::class);
        $sessionUserMock->shouldReceive('getId')->andReturn(1);
        $sessionUserMock->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUserMock->shouldReceive('isRoot')->andReturn(true);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUserMock)
            ->andReturn(true);
        $token = $this->manager->createUserTokenForSessionUser($sessionUserMock, $ttl);

        $this->assertSame(
            $token->expiresAt->getTimestamp(),
            $token->issuedAt->add($maxTtlInterval)->getTimestamp()
        );
    }

    public function testRefreshUserToken(): void
    {
        $iat = new DateTimeImmutable();
        $exp = $iat->add(new DateInterval('PT30M'));
        $firstCreatedAt = $iat->sub(new DateInterval('PT20M'));
        $userId = 10;
        $iss = 'foo';
        $aud = ['biff', 'bob'];
        $refreshCount = 0;
        $oldToken = $this->factory->create(
            [
                'iat' => $iat->format('U'),
                'exp' => $exp->format('U'),
                'iss' => $iss,
                'aud' => $aud,
                'user_id' => $userId,
                'is_root' => false,
                'performs_non_learner_function' => false,
                'can_create_or_update_user_in_any_school' => false,
                'firstCreatedAt' => $firstCreatedAt->format('U'),
                'refreshCount' => $refreshCount,
            ]
        );
        assert($oldToken instanceof UserToken);
        $this->assertFalse($oldToken->isRoot);
        $this->assertFalse($oldToken->performsNonLearnerFunction);
        $this->assertFalse($oldToken->canCreateOrUpdateUserInAnySchool);

        $newTtl = 'PT45M';
        $sessionUserMock = m::mock(SessionUserInterface::class);
        $sessionUserMock->shouldReceive('getId')->andReturn($userId);
        $sessionUserMock->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $sessionUserMock->shouldReceive('isRoot')->andReturn(true);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUserMock)
            ->andReturn(true);
        $newToken = $this->manager->refreshUserToken($sessionUserMock, $oldToken, $newTtl);
        $this->assertSame(
            $newToken->expiresAt->getTimestamp(),
            $newToken->issuedAt->add(new DateInterval($newTtl))->getTimestamp(),
        );
        $this->assertSame($newToken->issuer, $oldToken->issuer);
        $this->assertSame($newToken->audience, $newToken->audience);
        $this->assertSame($newToken->userId, $userId);
        $this->assertTrue($newToken->isRoot);
        $this->assertTrue($newToken->performsNonLearnerFunction);
        $this->assertTrue($newToken->canCreateOrUpdateUserInAnySchool);
        $this->assertSame($newToken->firstCreatedAt->getTimestamp(), $oldToken->firstCreatedAt->getTimestamp());
        $this->assertSame($newToken->refreshCount, $oldToken->refreshCount + 1);
    }

    #[DataProvider('createUserTokenFromServiceTokenProvider')]
    public function testCreateUserTokenFromServiceToken(
        int $userId,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        bool $canCreateOrUpdateUsersInAnySchool,
        int $serviceTokenId,
        array $serviceTokenAudience,
    ): void {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('getId')->andReturn($userId);
        $sessionUser->shouldReceive('isRoot')->andReturn($isRoot);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)
            ->andReturn($canCreateOrUpdateUsersInAnySchool);
        $serviceToken = new ServiceToken(
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('PT5M')),
            $serviceTokenAudience,
            'doesnotmatter',
            $serviceTokenId,
            [],
            true,
        );

        $userToken = $this->manager->createUserTokenFromServiceToken($sessionUser, $serviceToken);
        $this->assertSame(
            $userToken->issuedAt->add(new DateInterval(TokenManager::USER_TOKEN_SHORT_TTL))->getTimestamp(),
            $userToken->expiresAt->getTimestamp()
        );
        $this->assertSame(TokenManager::TOKEN_DEFAULT_ISSUER, $userToken->issuer);
        $this->assertSame($serviceTokenAudience, $userToken->audience);
        $this->assertSame($isRoot, $userToken->isRoot);
        $this->assertSame($performsNonLearnerFunction, $userToken->performsNonLearnerFunction);
        $this->assertSame($canCreateOrUpdateUsersInAnySchool, $userToken->canCreateOrUpdateUserInAnySchool);
        $this->assertSame($userToken->issuedAt->getTimestamp(), $userToken->firstCreatedAt->getTimestamp());
        $this->assertSame(0, $userToken->refreshCount);
        $this->assertSame($serviceTokenId, $userToken->issuedWith);
    }

    public static function createUserTokenFromServiceTokenProvider(): array
    {
        return [
            [10, true, false, true, 5, []],
            [20, false, true, false, 10, ['ilios', 'fizz']],
        ];
    }
}
