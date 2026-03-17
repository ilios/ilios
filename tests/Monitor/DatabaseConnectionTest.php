<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\DatabaseConnection;
use App\Tests\TestCase;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Mockery as m;

/**
 * @covers \App\Monitor\DatabaseConnection
 */
final class DatabaseConnectionTest extends TestCase
{
    public function testLabel(): void
    {
        $connection = m::mock(Connection::class);
        $check = new DatabaseConnection($connection);
        $this->assertEquals('Database connection', $check->getLabel());
    }

    public function testCheckSucceeds(): void
    {
        $platform = m::mock(AbstractPlatform::class);
        $platform->shouldReceive('getDummySelectSQL')->andReturn('whatever');
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform')->andReturn($platform);
        $connection->shouldReceive('fetchOne')->andReturn(false);
        $check = new DatabaseConnection($connection);
        $result = $check->check();
        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals('Database connection is up.', $result->getMessage());
    }

    public function testCheckFails(): void
    {
        $platform = m::mock(AbstractPlatform::class);
        $platform->shouldReceive('getDummySelectSQL')->andReturn('whatever');
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform')->andReturn($platform);
        $connection->shouldReceive('fetchOne')->andThrow(ConnectionException::class);
        $check = new DatabaseConnection($connection);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('Database connection is down.', $result->getMessage());
    }
}
