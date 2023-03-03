<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\DropCommand;
use App\Service\Index\Manager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @group cli
 */
class DropCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $indexManager;
    protected m\MockInterface $entityManager;
    public function setUp(): void
    {
        parent::setUp();
        $this->indexManager = m::mock(Manager::class);
        $this->entityManager = m::mock(EntityManagerInterface::class);

        $command = new DropCommand($this->indexManager, $this->entityManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(DropCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->indexManager);
        unset($this->entityManager);
    }

    public function testRequireForce()
    {
        $this->commandTester->execute([
            'command' => DropCommand::COMMAND_NAME,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Please run the operation with --force to execute/',
            $output
        );
    }

    public function testDropsWithForce()
    {
        $this->indexManager->shouldReceive('drop')->once();
        $connection = m::mock(Connection::class);
        $connection
            ->shouldReceive('executeStatement')
            ->with('DELETE FROM messenger_messages WHERE queue_name="default"');
        $this->entityManager->shouldReceive('getConnection')->andReturn($connection);

        $this->commandTester->execute([
            'command' => DropCommand::COMMAND_NAME,
            '--force' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ok./',
            $output
        );
    }
}
