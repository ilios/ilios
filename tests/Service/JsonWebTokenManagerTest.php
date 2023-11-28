<?php

declare(strict_types=1);

namespace App\Tests\Service;

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

/**
 * @coversDefaultClass \App\Service\JsonWebTokenManager
 */
class JsonWebTokenManagerTest extends TestCase
{
    protected JsonWebTokenManager $obj;
    protected m\MockInterface $permissionChecker;
    protected m\MockInterface $sessionUserProvider;
    protected m\MockInterface $serviceTokenUserProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->serviceTokenUserProvider = m::mock(ServiceTokenUserProvider::class);
        $this->obj = new JsonWebTokenManager(
            $this->permissionChecker,
            $this->sessionUserProvider,
            $this->serviceTokenUserProvider,
            'secret'
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->permissionChecker);
        unset($this->sessionUserProvider);
        unset($this->serviceTokenUserProvider);
    }

    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof JsonWebTokenManager);
    }

    public function testGetUserIdFromToken()
    {
        $jwt = $this->buildUserJwt();
        $this->assertSame(42, $this->obj->getUserIdFromToken($jwt));
    }

    public function testGetUserIdFromTokenString()
    {
        $jwt = $this->buildUserJwt(['user_id' => '123']);
        $this->assertSame(123, $this->obj->getUserIdFromToken($jwt));
    }

    public function testGetIssuedAtFromToken()
    {
        $yesterday = new DateTime('yesterday');
        $stamp = $yesterday->format('U');
        $jwt = $this->buildUserJwt(['iat' => $stamp]);
        $this->assertSame($stamp, $this->obj->getIssuedAtFromToken($jwt)->format('U'));

        $jwt = $this->buildUserJwt(['iat' => (int) $stamp]);
        $this->assertSame($stamp, $this->obj->getIssuedAtFromToken($jwt)->format('U'));
    }

    public function testGetExpiresAtFromToken()
    {
        $tomorrow = new DateTime('tomorrow');
        $stamp = $tomorrow->format('U');
        $jwt = $this->buildUserJwt(['exp' => $stamp]);
        $this->assertSame($stamp, $this->obj->getExpiresAtFromToken($jwt)->format('U'));

        $jwt = $this->buildUserJwt(['exp' => (int) $stamp]);
        $this->assertSame($stamp, $this->obj->getExpiresAtFromToken($jwt)->format('U'));
    }

    public function testUserTokensGetUserPermissions()
    {
        $jwt = $this->buildUserJwt();
        $this->assertSame('user', $this->obj->getPermissionsFromToken($jwt));
    }

    public function testCreateJwtFromSessionUser()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
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

    public function testCreateJwtFromSessionUserWhichExpiresNextWeek()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P1W');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->d > 5);
    }

    public function testCreateJwtFromSessionUserWhichExpiresAfterMaximumTime()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
        $sessionUser->shouldReceive('isRoot')->once()->andReturn(true);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->once()->andReturn(true);
        $this->permissionChecker->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUser)->once()->andReturn(true);

        $jwt = $this->obj->createJwtFromSessionUser($sessionUser, 'P400D');
        $now = new DateTime();
        $expiresAt = $this->obj->getExpiresAtFromToken($jwt);

        $this->assertTrue($now->diff($expiresAt)->days < 365, 'maximum ttl not applied');
    }

    public function testCreateJwtFromSessionUserWithLessPrivileges()
    {
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->mock();
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

    /**
     * @covers ::createJwtFromServiceTokenUser
     */
    public function testCreateJwtFromServiceTokenUser()
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

    public function getWriteableSchoolIdsFromTokenProvider(): array
    {
        return [
            [$this->buildJwt(), []],
            [$this->buildUserJwt(), []],
            [$this->buildUserJwt(['writeable_schools' => [1, 2, 3]]), []],
            [$this->buildServiceTokenJwt(), []],
            [$this->buildServiceTokenJwt(['writeable_schools' => []]), []],
            [$this->buildServiceTokenJwt(['writeable_schools' => '1,2,3']), []],
            [$this->buildServiceTokenJwt(['writeable_schools' => [1, 2, 3]]), [1, 2, 3]],
        ];
    }

    /**
     * @covers ::getWriteableSchoolIdsFromToken
     * @dataProvider getWriteableSchoolIdsFromTokenProvider
     */
    public function testGetWriteableSchoolIdsFromToken(string $jwt, array $schoolIds): void
    {
        $this->assertEquals($this->obj->getWriteableSchoolIdsFromToken($jwt), $schoolIds);
    }

    public function isUserTokenProvider(): array
    {
        return [
            [$this->buildJwt(), false,],
            [$this->buildServiceTokenJwt(), false],
            [$this->buildUserJwt(), true],
        ];
    }

    /**
     * @covers ::isUserToken
     * @dataProvider isUserTokenProvider
     */
    public function testIsUserToken(string $jwt, bool $expected): void
    {
        $this->assertEquals($this->obj->isUserToken($jwt), $expected);
    }

    public function isServiceTokenProvider(): array
    {
        return [
            [$this->buildJwt(), false,],
            [$this->buildUserJwt(), false],
            [$this->buildServiceTokenJwt(), true],
        ];
    }

    /**
     * @covers ::isServiceToken
     * @dataProvider isServiceTokenProvider
     */
    public function testIsServiceToken(string $jwt, bool $expected): void
    {
        $this->assertEquals($this->obj->isServiceToken($jwt), $expected);
    }

    protected function buildUserJwt(array $values = [], $secretKey = 'ilios.jwt.key.secret'): string
    {
        return $this->buildJwt(array_merge([JsonWebTokenManager::USER_ID_KEY => 42], $values), $secretKey);
    }

    protected function buildServiceTokenJwt(array $values = [], $secretKey = 'ilios.jwt.key.secret'): string
    {
        return $this->buildJwt(array_merge([JsonWebTokenManager::TOKEN_ID_KEY => 12], $values), $secretKey);
    }

    protected function buildJwt(array $values = [], $secretKey = 'ilios.jwt.key.secret'): string
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
