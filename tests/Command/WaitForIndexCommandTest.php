<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Exception;
use PHPUnit\Framework\Attributes\Group;
use App\Command\WaitForIndexCommand;
use App\Service\Index\Manager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use OpenSearch\Client;
use OpenSearch\Namespaces\NodesNamespace;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Stopwatch\Stopwatch;

#[Group('cli')]
class WaitForIndexCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $client;
    protected m\MockInterface $indexManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->indexManager = m::mock(Manager::class);
        $command = new WaitForIndexCommand($this->client, $this->indexManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->indexManager);
        unset($this->commandTester);
    }

    public function testReturnsWhenIndexIsWorking(): void
    {
        $nodes = m::mock(NodesNamespace::class)->shouldReceive('info')->andReturn();
        $this->client->shouldReceive('nodes')->andReturn($nodes->getMock());
        $this->indexManager->shouldReceive('hasBeenCreated')->andReturn(true);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForConnectionTransportException(): void
    {
        $nodes = m::mock(NodesNamespace::class);
        $nodes->shouldReceive('info')->times(2)->andThrow(TransportException::class);
        $this->client->shouldReceive('nodes')->andReturn($nodes);
        $this->indexManager->shouldReceive('hasBeenCreated')->andReturn(true);
        $nodes->shouldReceive('info')->once()->andReturn();

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForConnectionException(): void
    {
        $nodes = m::mock(NodesNamespace::class);
        $e = new Exception(previous: new TransportException());
        $nodes->shouldReceive('info')->times(2)->andThrow($e);
        $this->client->shouldReceive('nodes')->andReturn($nodes);
        $this->indexManager->shouldReceive('hasBeenCreated')->andReturn(true);
        $nodes->shouldReceive('info')->once()->andReturn();

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForIndexesToBeCreated(): void
    {
        $nodes = m::mock(NodesNamespace::class)->shouldReceive('info')->andReturn();
        $this->client->shouldReceive('nodes')->andReturn($nodes->getMock());
        $this->indexManager->shouldReceive('hasBeenCreated')->times(2)->andReturn(false);
        $this->indexManager->shouldReceive('hasBeenCreated')->once()->andReturn(true);

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testThrowsOtherException(): void
    {
        $nodes = m::mock(NodesNamespace::class);
        $nodes->shouldReceive('info')->andThrow(Exception::class);
        $this->client->shouldReceive('nodes')->andReturn($nodes);
        $this->expectException(Exception::class);
        $this->commandTester->execute([]);
    }
}
