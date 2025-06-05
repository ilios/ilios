<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\ServiceTokenUser;
use App\Classes\SessionUser;
use App\Entity\AuthenticationInterface;
use App\Entity\School;
use App\Entity\SchoolInterface;
use App\Entity\User;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Tests\TestCase;
use DateTime;
use Mockery as m;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

#[CoversClass(SessionUserProvider::class)]
final class SessionUserProviderTest extends TestCase
{
    protected SessionUserProvider $provider;
    protected m\MockInterface $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = m::mock(UserRepository::class);
        $this->provider = new SessionUserProvider($this->repositoryMock);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->repositoryMock);
        unset($this->provider);
    }

    public static function supportsClassProvider(): array
    {
        return [
            [SessionUser::class, true],
            [ServiceTokenUser::class, false],
        ];
    }

    #[DataProvider('supportsClassProvider')]
    public function testSupportsClass(string $className, bool $expected): void
    {
        $this->assertEquals($expected, $this->provider->supportsClass($className));
    }

    public function testLoadByIdentifier(): void
    {
        $id = 10;
        $schoolId = 23;
        $authMock = m::mock(AuthenticationInterface::class);
        $authMock->shouldReceive('getPassword')->andReturn('whatever');
        $authMock->shouldReceive('getInvalidateTokenIssuedBefore')->andReturn(new DateTime());
        $schoolMock = m::mock(SchoolInterface::class);
        $schoolMock->shouldReceive('getId')->andReturn($schoolId);
        $entityMock = m::mock(UserInterface::class);
        $entityMock->shouldReceive('getId')->andReturn($id);
        $entityMock->shouldReceive('isEnabled')->andReturn(true);
        $entityMock->shouldReceive('getAuthentication')->andReturn($authMock);
        $entityMock->shouldReceive(('isRoot'))->andReturn(true);
        $entityMock->shouldReceive('getSchool')->andReturn($schoolMock);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn($entityMock);
        $token = $this->provider->loadUserByIdentifier((string) $id);
        $this->assertEquals($id, $token->getId());
        $this->assertTrue($token->isEnabled());
    }

    public function testLoadByIdentifierFailsOnUserNotFound(): void
    {
        $id = 10;
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn(null);
        $this->expectExceptionMessage("Username \"{$id}\" does not exist.");
        $this->provider->loadUserByIdentifier((string) $id);
    }

    public function testRefreshUser(): void
    {
        $id = 10;
        $schoolId = 23;
        $sessionUserMock = m::mock(SessionUser::class);
        $sessionUserMock->shouldReceive('getUserIdentifier')->andReturn((string) $id);
        $authMock = m::mock(AuthenticationInterface::class);
        $authMock->shouldReceive('getPassword')->andReturn('whatever');
        $authMock->shouldReceive('getInvalidateTokenIssuedBefore')->andReturn(new DateTime());
        $schoolMock = m::mock(SchoolInterface::class);
        $schoolMock->shouldReceive('getId')->andReturn($schoolId);
        $newEntityMock = m::mock(UserInterface::class);
        $newEntityMock->shouldReceive('getId')->andReturn($id);
        $newEntityMock->shouldReceive('isEnabled')->andReturn(true);
        $newEntityMock->shouldReceive('getAuthentication')->andReturn($authMock);
        $newEntityMock->shouldReceive(('isRoot'))->andReturn(true);
        $newEntityMock->shouldReceive('getSchool')->andReturn($schoolMock);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn($newEntityMock);

        $newSessionUser = $this->provider->refreshUser($sessionUserMock);
        $this->assertEquals((string) $id, $newSessionUser->getUserIdentifier());
    }

    public function testRefreshUserFailsOnUserNotFound(): void
    {
        $id = 10;
        $sessionUserMock = m::mock(SessionUser::class);
        $sessionUserMock->shouldReceive('getUserIdentifier')->andReturn((string) $id);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn(null);
        $this->expectExceptionMessage("Username \"{$id}\" does not exist.");
        $this->provider->refreshUser($sessionUserMock);
    }

    public function testRefreshUserFailsOnWrongClass(): void
    {
        $serviceTokenUserMock = m::mock(ServiceTokenUser::class);
        $this->expectException(UnsupportedUserException::class);
        $this->provider->refreshUser($serviceTokenUserMock);
    }

    public function testCreateSessionUserFromUser(): void
    {
        $userId = 10;
        $schoolId = 1;
        $user = new User();
        $school = new School();
        $school->setId($schoolId);
        $user->setId($userId);
        $user->setSchool($school);
        $sessionUser = $this->provider->createSessionUserFromUser($user);
        $this->assertInstanceOf(SessionUser::class, $sessionUser);
        $this->assertEquals($userId, $sessionUser->getId());
        $this->assertEquals($schoolId, $sessionUser->getSchoolId());
    }

    public function testCreateSessionUserFromUserId(): void
    {
        $userId = 10;
        $schoolId = 1;
        $user = new User();
        $school = new School();
        $school->setId($schoolId);
        $user->setId($userId);
        $user->setSchool($school);
        $this->repositoryMock->shouldReceive('findOneBy', ['id' => $userId])->andReturn($user);
        $sessionUser = $this->provider->createSessionUserFromUserId($userId);
        $this->assertInstanceOf(SessionUser::class, $sessionUser);
        $this->assertEquals($userId, $sessionUser->getId());
        $this->assertEquals($schoolId, $sessionUser->getSchoolId());
    }
}
