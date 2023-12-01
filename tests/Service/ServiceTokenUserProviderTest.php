<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\ServiceTokenUser;
use App\Classes\SessionUser;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use App\Service\ServiceTokenUserProvider;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @coversDefaultClass \App\Service\ServiceTokenUserProvider
 */
class ServiceTokenUserProviderTest extends TestCase
{
    protected ServiceTokenUserProvider $provider;
    protected m\MockInterface $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = m::mock(ServiceTokenRepository::class);
        $this->provider = new ServiceTokenUserProvider($this->repositoryMock);
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
            [ServiceTokenUser::class, true],
            [SessionUser::class, false],
        ];
    }

    /**
     * @covers ::supportsClass
     * @dataProvider supportsClassProvider
     */
    public function testSupportsClass(string $className, bool $expected): void
    {
        $this->assertEquals($expected, $this->provider->supportsClass($className));
    }

    /**
     * @covers ::loadUserByIdentifier
     */
    public function testLoadByIdentifier(): void
    {
        $id = 10;
        $entityMock = m::mock(ServiceTokenInterface::class);
        $entityMock->shouldReceive('getId')->andReturn($id);
        $entityMock->shouldReceive('isEnabled')->andReturn(true);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn($entityMock);
        $token = $this->provider->loadUserByIdentifier((string) $id);
        $this->assertEquals($id, $token->getId());
        $this->assertTrue($token->isEnabled());
    }

    /**
     * @covers ::loadUserByIdentifier
     */
    public function testLoadByIdentifierFailsOnTokenNotFound(): void
    {
        $id = 10;
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn(null);
        $this->expectExceptionMessage("Service token \"{$id}\" does not exist.");
        $this->provider->loadUserByIdentifier((string) $id);
    }

    /**
     * @covers ::refreshUser
     */
    public function testRefreshUser(): void
    {
        $id = 10;
        $serviceTokenUserMock = m::mock(ServiceTokenUser::class);
        $serviceTokenUserMock->shouldReceive('getUserIdentifier')->andReturn((string) $id);
        $newEntityMock = m::mock(ServiceTokenInterface::class);
        $newEntityMock->shouldReceive('getId')->andReturn($id);
        $newEntityMock->shouldReceive('isEnabled')->andReturn(true);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn($newEntityMock);

        $newServiceTokenUser = $this->provider->refreshUser($serviceTokenUserMock);
        $this->assertEquals((string) $id, $newServiceTokenUser->getUserIdentifier());
    }

    /**
     * @covers ::refreshUser
     */
    public function testRefreshUserFailsOnNoTokenFound(): void
    {
        $id = 10;
        $serviceTokenUserMock = m::mock(ServiceTokenUser::class);
        $serviceTokenUserMock->shouldReceive('getUserIdentifier')->andReturn((string) $id);
        $this->repositoryMock->shouldReceive('findOneBy')->with(['id' => $id])->andReturn(null);
        $this->expectExceptionMessage("Service token \"{$id}\" does not exist.");
        $this->provider->refreshUser($serviceTokenUserMock);
    }

    /**
     * @covers ::refreshUser
     */
    public function testRefreshUserFailsOnWrongClass(): void
    {
        $serviceTokenUserMock = m::mock(SessionUser::class);
        $this->expectException(UnsupportedUserException::class);
        $this->provider->refreshUser($serviceTokenUserMock);
    }
}
