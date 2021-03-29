<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\DropCommand;
use App\Service\Index\Manager;
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

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    protected $indexManager;


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
