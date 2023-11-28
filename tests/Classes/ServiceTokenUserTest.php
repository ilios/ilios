<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\ServiceTokenUser;
use App\Entity\ServiceTokenInterface;
use App\Tests\TestCase;
use DateTime;
use Mockery as m;

/**
 * @coversDefaultClass \App\Classes\ServiceTokenUser
 */
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

    /**
     * @covers ::getId
     */
    public function testGetId(): void
    {
        $this->mockServiceToken->shouldReceive('getId')->andReturn(1);
        $this->assertEquals(1, $this->serviceTokenUser->getId());
    }

    /**
     * @covers ::getUserIdentifier
     */
    public function testGetUserIdentifier(): void
    {
        $this->mockServiceToken->shouldReceive('getId')->andReturn(1);
        $this->assertEquals('1', $this->serviceTokenUser->getUserIdentifier());
    }

    /**
     * @covers ::getExpiresAt
     */
    public function testGetExpiredAt(): void
    {
        $date = new DateTime();
        $this->mockServiceToken->shouldReceive('getExpiresAt')->andReturn($date);
        $this->assertEquals($date, $this->serviceTokenUser->getExpiresAt());
    }

    /**
     * @covers ::getCreatedAt
     */
    public function testGetCreatedAt(): void
    {
        $date = new DateTime();
        $this->mockServiceToken->shouldReceive('getCreatedAt')->andReturn($date);
        $this->assertEquals($date, $this->serviceTokenUser->getCreatedAt());
    }

    /**
     * @covers ::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->mockServiceToken->shouldReceive('isEnabled')->andReturn(false);
        $this->assertFalse($this->serviceTokenUser->isEnabled());
    }
}
