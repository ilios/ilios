<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\DropCommand;
use App\Service\Index\Manager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @group cli
 */
class DropCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $indexManager;


    public function setUp(): void
    {
        parent::setUp();
        $this->indexManager = m::mock(Manager::class);

        $command = new DropCommand($this->indexManager);
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
        unset($this->commandTester);
    }

    public function testRequireForce(): void
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

    public function testDropsWithForce(): void
    {
        $this->indexManager->shouldReceive('drop')->once();

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
