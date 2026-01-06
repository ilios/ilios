<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\WaitForDatabaseCommand;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Stopwatch\Stopwatch;

#[Group('cli')]
final class WaitForDatabaseCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    private m\MockInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = m::mock(EntityManagerInterface::class);
        $command = new WaitForDatabaseCommand($this->entityManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->entityManager);
        unset($this->commandTester);
    }

    public function testReturnsWhenDbIsWorking(): void
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')->with('Select 1');
        $this->entityManager->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForConnection(): void
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')->with('Select 1')
            ->times(2)->andThrow(m::mock(ConnectionException::class));
        $connection->shouldReceive('executeQuery')->once()->with('Select 1');
        $this->entityManager->shouldReceive('getConnection')->andReturn($connection);

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
