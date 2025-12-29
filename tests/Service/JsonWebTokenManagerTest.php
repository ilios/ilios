<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\SecretManager;
use Firebase\JWT\SignatureInvalidException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Service\ServiceTokenUserProvider;
use App\Service\SessionUserPermissionChecker;
use App\Service\SessionUserProvider;
use App\Tests\TestCase;
use DateInterval;
use Firebase\JWT\JWT;
use DateTime;
use Mockery as m;
use App\Service\JsonWebTokenManager;

#[CoversClass(JsonWebTokenManager::class)]
final class JsonWebTokenManagerTest extends TestCase
{
    protected const string SECRET = 'LongEnoughTestSecret';
    protected const string DEFAULT_SECRET_KEY = JsonWebTokenManager::PREPEND_KEY . self::SECRET;
    protected JsonWebTokenManager $obj;
    protected m\MockInterface $permissionChecker;
    protected m\MockInterface $sessionUserProvider;
    protected m\MockInterface $serviceTokenUserProvider;
    protected m\MockInterface | SecretManager $secretManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->serviceTokenUserProvider = m::mock(ServiceTokenUserProvider::class);
        $this->secretManager = m::mock(SecretManager::class);
        $this->secretManager->expects('getSecret')->once()->andReturn(self::SECRET);
        $this->obj = new JsonWebTokenManager(
            $this->permissionChecker,
            $this->sessionUserProvider,
            $this->serviceTokenUserProvider,
            $this->secretManager,
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->permissionChecker);
        unset($this->sessionUserProvider);
        unset($this->serviceTokenUserProvider);
        unset($this->secretManager);
    }

    public function testGetUserIdFromToken(): void
    {
        $jwt = $this->buildUserJwt();
        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
    }

    public function testGetUserIdFromTokenString(): void
    {
        $jwt = $this->buildUserJwt(['user_id' => '123']);
        $this->assertSame(123, $this->obj->getUserIdFromToken($jwt));
    }

    public function testGetIssuedAtFromToken(): void
    {
        $yesterday = new DateTime('yesterday');
        $stamp = $yesterday->format('U');
        $jwt = $this->buildUserJwt(['iat' => $stamp]);
        $this->assertSame($stamp, $this->obj->getIssuedAtFromToken($jwt)->format('U'));

        $jwt = $this->buildUserJwt(['iat' => (int) $stamp]);
        $this->assertSame($stamp, $this->obj->getIssuedAtFromToken($jwt)->format('U'));
    }

    public function testGetExpiresAtFromToken(): void
    {
        $tomorrow = new DateTime('tomorrow');
        $stamp = $tomorrow->format('U');
        $jwt = $this->buildUserJwt(['exp' => $stamp]);
        $this->assertSame($stamp, $this->obj->getExpiresAtFromToken($jwt)->format('U'));

        $jwt = $this->buildUserJwt(['exp' => (int) $stamp]);
        $this->assertSame($stamp, $this->obj->getExpiresAtFromToken($jwt)->format('U'));
    }

    public function testUserTokensGetUserPermissions(): void
    {
        $jwt = $this->buildUserJwt();
        $this->assertSame('user', $this->obj->getPermissionsFromToken($jwt));
    }

    public function testCreateJwtFromSessionUser(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('getId')->andReturn(42);

        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser);

        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
        $this->assertSame(true, $this->obj->getPerformsNonLearnerFunctionFromToken($jwt));
        $this->assertSame(true, $this->obj->getIsRootFromToken($jwt));
        $this->assertSame(true, $this->obj->getCanCreateOrUpdateUserInAnySchoolFromToken($jwt));
    }

    public function testCreateJwtFromSessionUserWhichExpiresNextWeek(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('getId')->andReturn(42);

        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P1W');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }

    public function testCreateJwtFromSessionUserWhichExpiresAfterMaximumTime(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('getId')->andReturn(42);

        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P400D');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->days < 365, 'maximum ttl not applied');
    }

    public function testCreateJwtFromSessionUserWithLessPrivileges(): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('getId')->andReturn(42);

        $sessionUser->shouldReceive('isRoot')->once()->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(false);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(false);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser);

        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
        $this->assertSame(false, $this->obj->getIsRootFromToken($jwt));
        $this->assertSame(false, $this->obj->getPerformsNonLearnerFunctionFromToken($jwt));
        $this->assertSame(false, $this->obj->getCanCreateOrUpdateUserInAnySchoolFromToken($jwt));
    }

    public function testCreateJwtFromServiceTokenUser(): void
    {
        $schoolIds = [1, 3];
        $tokenId = 67;
        $issuedAt = new DateTime();
        $expiresAt = (clone $issuedAt)->add(new DateInterval('P30D'));
        $tokenUser = m::mock(ServiceTokenUserInterface::class);
        $tokenUser->shouldReceive('getId')->andReturn($tokenId);
        $tokenUser->shouldReceive('getCreatedAt')->andReturn($issuedAt);
        $tokenUser->shouldReceive('getExpiresAt')->andReturn($expiresAt);
        $jwt = $this->obj->createJwtFromServiceTokenUser($tokenUser, $schoolIds);
        $this->assertTrue($this->obj->isServiceToken($jwt));
        $this->assertEquals($this->obj->getServiceTokenIdFromToken($jwt), $tokenId);
        $this->assertEquals($this->obj->getWriteableSchoolIdsFromToken($jwt), $schoolIds);
        $this->assertEquals($this->obj->getIssuedAtFromToken($jwt)->getTimestamp(), $issuedAt->getTimestamp());
        $this->assertEquals($this->obj->getExpiresAtFromToken($jwt)->getTimestamp(), $expiresAt->getTimestamp());
    }

    public static function getWriteableSchoolIdsFromTokenProvider(): array
    {
        return [
            [self::buildJwt(), []],
            [self::buildUserJwt(), []],
            [self::buildUserJwt(['writeable_schools' => [1, 2, 3]]), []],
            [self::buildServiceTokenJwt(), []],
            [self::buildServiceTokenJwt(['writeable_schools' => []]), []],
            [self::buildServiceTokenJwt(['writeable_schools' => '1,2,3']), []],
            [self::buildServiceTokenJwt(['writeable_schools' => [1, 2, 3]]), [1, 2, 3]],
        ];
    }

    #[DataProvider('getWriteableSchoolIdsFromTokenProvider')]
    public function testGetWriteableSchoolIdsFromToken(string $jwt, array $schoolIds): void
    {
        $this->assertEquals($this->obj->getWriteableSchoolIdsFromToken($jwt), $schoolIds);
    }

    public static function isUserTokenProvider(): array
    {
        return [
            [self::buildJwt(), false],
            [self::buildServiceTokenJwt(), false],
            [self::buildUserJwt(), true],
        ];
    }

    #[DataProvider('isUserTokenProvider')]
    public function testIsUserToken(string $jwt, bool $expected): void
    {
        $this->assertEquals($this->obj->isUserToken($jwt), $expected);
    }

    public static function isServiceTokenProvider(): array
    {
        return [
            [self::buildJwt(), false,],
            [self::buildUserJwt(), false],
            [self::buildServiceTokenJwt(), true],
        ];
    }

    #[DataProvider('isServiceTokenProvider')]
    public function testIsServiceToken(string $jwt, bool $expected): void
    {
        $this->assertEquals($this->obj->isServiceToken($jwt), $expected);
    }
    public function testGetUserIdFromTokenStringUsingTransitionalSecret(): void
    {
        $transitionSecret = self::SECRET . '-transitional';
        $jwt = $this->buildUserJwt(['user_id' => '123'], JsonWebTokenManager::PREPEND_KEY . $transitionSecret);
        $this->secretManager->shouldReceive('getTransitionalSecret')->once()->andReturn($transitionSecret);
        $this->assertSame(123, $this->obj->getUserIdFromToken($jwt));
    }
    public function testUnableToDecodeTransitionalSecret(): void
    {
        $transitionSecret = self::SECRET . '-transitional';
        $jwt = $this->buildUserJwt(['user_id' => '123'], JsonWebTokenManager::PREPEND_KEY . $transitionSecret);
        $this->secretManager->shouldReceive('getTransitionalSecret')->once()->andReturn(null);
        $this->expectException(SignatureInvalidException::class);
        $this->obj->getUserIdFromToken($jwt);
    }
    public function testUnableToDecodeWrongTransitionalSecret(): void
    {
        $transitionSecret = self::SECRET . '-transitional';
        $jwt = $this->buildUserJwt(['user_id' => '123'], JsonWebTokenManager::PREPEND_KEY . $transitionSecret);
        $this->secretManager->shouldReceive('getTransitionalSecret')->once()->andReturn('wrong');
        $this->expectException(SignatureInvalidException::class);
        $this->obj->getUserIdFromToken($jwt);
    }

    protected static function buildUserJwt(array $values = [], string $secretKey = self::DEFAULT_SECRET_KEY): string
    {
        return self::buildJwt(array_merge([JsonWebTokenManager::USER_ID_KEY => 42], $values), $secretKey);
    }

    protected static function buildServiceTokenJwt(
        array $values = [],
        string $secretKey = self::DEFAULT_SECRET_KEY,
    ): string {
        return self::buildJwt(array_merge([JsonWebTokenManager::TOKEN_ID_KEY => 12], $values), $secretKey);
    }

    protected static function buildJwt(array $values = [], string $secretKey = self::DEFAULT_SECRET_KEY): string
    {
        $now = new DateTime();
        $default = [
            'iss' => 'ilios',
            'aud' => 'ilios',
            'iat' => $now->format('U'),
            'exp' => $now->modify('+1 year')->format('U'),
        ];

        $merged = array_merge($default, $values);

        return JWT::encode($merged, $secretKey, JsonWebTokenManager::SIGNING_ALGORITHM);
    }
}
