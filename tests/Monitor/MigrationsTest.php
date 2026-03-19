<?php

declare(strict_types=1);

namespace App\Tests\Monitor;

use App\Monitor\Migrations;
use App\Tests\TestCase;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\AvailableMigrationsList;
use Doctrine\Migrations\Metadata\ExecutedMigrationsList;
use Doctrine\Migrations\Version\MigrationStatusCalculator;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Mockery as m;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Migrations::class)]
final class MigrationsTest extends TestCase
{
    public function testLabel(): void
    {
        $factory = m::mock(DependencyFactory::class);
        $check = new Migrations($factory);
        $this->assertEquals('Database migrations', $check->getLabel());
    }

    public function testCheckSucceeds(): void
    {
        $statusCalculator = m::mock(MigrationStatusCalculator::class);
        $statusCalculator->shouldReceive('getExecutedUnavailableMigrations')->andReturn(new ExecutedMigrationsList([]));
        $statusCalculator->shouldReceive('getNewMigrations')->andReturn(new AvailableMigrationsList([]));
        $factory = m::mock(DependencyFactory::class);
        $factory->shouldReceive('getMigrationStatusCalculator')->andReturn($statusCalculator);
        $check = new Migrations($factory);
        $result = $check->check();
        $this->assertInstanceOf(Success::class, $result);
        $this->assertEquals('Up-to-date! No migrations to execute.', $result->getMessage());
    }

    public function testCheckFailsDueToUnprocessedMigrations(): void
    {
        $migration = m::mock(AbstractMigration::class);
        $statusCalculator = m::mock(MigrationStatusCalculator::class);
        $statusCalculator->shouldReceive('getExecutedUnavailableMigrations')->andReturn(new ExecutedMigrationsList([$migration]));
        $statusCalculator->shouldReceive('getNewMigrations')->andReturn(new AvailableMigrationsList([]));
        $factory = m::mock(DependencyFactory::class);
        $factory->shouldReceive('getMigrationStatusCalculator')->andReturn($statusCalculator);
        $check = new Migrations($factory);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('You have previously executed migrations in the database that are not registered migrations.', $result->getMessage());
    }
    public function testCheckFailsDueToUnavailableMigrations(): void
    {
        $migration = m::mock(AbstractMigration::class);
        $statusCalculator = m::mock(MigrationStatusCalculator::class);
        $statusCalculator->shouldReceive('getExecutedUnavailableMigrations')->andReturn(new ExecutedMigrationsList([]));
        $statusCalculator->shouldReceive('getNewMigrations')->andReturn(new AvailableMigrationsList([$migration]));
        $factory = m::mock(DependencyFactory::class);
        $factory->shouldReceive('getMigrationStatusCalculator')->andReturn($statusCalculator);
        $check = new Migrations($factory);
        $result = $check->check();
        $this->assertInstanceOf(Failure::class, $result);
        $this->assertEquals('Out-of-date! New migrations available to execute.', $result->getMessage());
    }
}
