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
        $maxTtlInterval = new DateInterval(TokenManager::TOKEN_MAX_TTL);
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
}
