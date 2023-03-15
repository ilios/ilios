<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\WaitForIndexCommand;
use App\Service\Index\Manager;
use OpenSearch\Client;
use OpenSearch\Common\Exceptions\NoNodesAvailableException;
use OpenSearch\Namespaces\NodesNamespace;
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
class WaitForIndexCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected Client|m\MockInterface $client;
    protected Manager|m\MockInterface $indexManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = m::mock(Client::class);
        $this->indexManager = m::mock(Manager::class);
        $command = new WaitForIndexCommand($this->client, $this->indexManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(WaitForIndexCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->indexManager);
    }

    public function testReturnsWhenIndexIsWorking()
    {
        $nodes = m::mock(NodesNamespace::class)->shouldReceive('info')->andReturn();
        $this->client->shouldReceive('nodes')->andReturn($nodes->getMock());
        $this->indexManager->shouldReceive('hasBeenCreated')->andReturn(true);

        $this->commandTester->execute([
            'command' => WaitForIndexCommand::COMMAND_NAME,
        ]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForConnection()
    {
        $nodes = m::mock(NodesNamespace::class)->shouldReceive('info')
            ->times(2)->andThrow(NoNodesAvailableException::class);
        $this->client->shouldReceive('nodes')->andReturn($nodes->getMock());
        $this->indexManager->shouldReceive('hasBeenCreated')->andReturn(true);
        $nodes->shouldReceive('info')->once()->andReturn();

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([
            'command' => WaitForIndexCommand::COMMAND_NAME,
        ]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testWaitsForIndexesToBeCreated()
    {
        $nodes = m::mock(NodesNamespace::class)->shouldReceive('info')->andReturn();
        $this->client->shouldReceive('nodes')->andReturn($nodes->getMock());
        $this->indexManager->shouldReceive('hasBeenCreated')->times(2)->andReturn(false);
        $this->indexManager->shouldReceive('hasBeenCreated')->once()->andReturn(true);

        $stopwatch = new Stopwatch();
        $stopwatch->start('test');
        $this->commandTester->execute([
            'command' => WaitForIndexCommand::COMMAND_NAME,
        ]);
        $duration = $stopwatch->stop('test')->getDuration();
        $this->assertGreaterThan(2000, $duration);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
