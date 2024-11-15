<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\ServiceTokenUser;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Entity\ServiceTokenInterface;
use App\Tests\TestCase;
use DateTime;
use Mockery as m;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(ServiceTokenUser::class)]
class ServiceTokenUserTest extends TestCase
{
    protected m\MockInterface $mockServiceToken;
    protected ServiceTokenUser $serviceTokenUser;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockServiceToken = m::mock(ServiceTokenInterface::class);
        $this->serviceTokenUser = new ServiceTokenUser($this->mockServiceToken);
    }

    public function tearDown(): void
    {
        unset($this->serviceTokenUser);
        unset($this->mockServiceToken);
        parent::tearDown();
    }

    public function testGetId(): void
    {
        $this->mockServiceToken->shouldReceive('getId')->andReturn(1);
        $this->assertEquals(1, $this->serviceTokenUser->getId());
    }

    public function testGetUserIdentifier(): void
    {
        $this->mockServiceToken->shouldReceive('getId')->andReturn(1);
        $this->assertEquals('1', $this->serviceTokenUser->getUserIdentifier());
    }

    public function testGetExpiredAt(): void
    {
        $date = new DateTime();
        $this->mockServiceToken->shouldReceive('getExpiresAt')->andReturn($date);
        $this->assertEquals($date, $this->serviceTokenUser->getExpiresAt());
    }

    public function testGetCreatedAt(): void
    {
        $date = new DateTime();
        $this->mockServiceToken->shouldReceive('getCreatedAt')->andReturn($date);
        $this->assertEquals($date, $this->serviceTokenUser->getCreatedAt());
    }

    public function testIsEnabled(): void
    {
        $this->mockServiceToken->shouldReceive('isEnabled')->andReturn(false);
        $this->assertFalse($this->serviceTokenUser->isEnabled());
    }

    #[DataProvider('isEqualToProvider')]
    public function testIsEqualTo(UserInterface $user, bool $expected): void
    {
        $this->mockServiceToken->shouldReceive('getId')->andReturn(1);
        $this->assertEquals($expected, $this->serviceTokenUser->isEqualTo($user));
    }

    public static function isEqualToProvider(): array
    {
        $sameTokenUser = m::mock(ServiceTokenUserInterface::class);
        $sameTokenUser->shouldReceive('getUserIdentifier')->andReturn(1);

        $otherTokenUser = m::mock(ServiceTokenUserInterface::class);
        $otherTokenUser->shouldReceive('getUserIdentifier')->andReturn(2);

        $sessionUser = m::mock(SessionUserInterface::class);

        return [
            [ $sameTokenUser, true ],
            [ $otherTokenUser, false ],
            [ $sessionUser, false ],
        ];
    }
}
