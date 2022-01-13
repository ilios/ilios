<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\WaitForDatabaseCommand;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @group cli
 *
 */
class WaitForDatabaseCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    private EntityManagerInterface|m\MockInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->entityManager = m::mock(EntityManagerInterface::class);
        $command = new WaitForDatabaseCommand($this->entityManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(WaitForDatabaseCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->entityManager);
    }

    public function testReturnsWhenDbIsWorking()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')->with('Select 1');
        $this->entityManager->shouldReceive('getConnection')->once()->andReturn($connection);

        $this->commandTester->execute([
            'command' => WaitForDatabaseCommand::COMMAND_NAME,
        ]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForConnection()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('executeQuery')->with('Select 1')
            ->times(2)->andThrow(m::mock(ConnectionException::class));
        $connection->shouldReceive('executeQuery')->once()->with('Select 1');
        $this->entityManager->shouldReceive('getConnection')->andReturn($connection);

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([
            'command' => WaitForDatabaseCommand::COMMAND_NAME,
        ]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
