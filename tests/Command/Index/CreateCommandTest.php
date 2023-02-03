<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\CreateCommand;
use App\Service\Index\Manager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @group cli
 */
class CreateCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $indexManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->indexManager = m::mock(Manager::class);

        $command = new CreateCommand($this->indexManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(CreateCommand::COMMAND_NAME);
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

    public function testCreateWithIndexDisabled()
    {
        $this->indexManager->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->indexManager->shouldNotReceive('create');

        $this->commandTester->execute([
            'command' => CreateCommand::COMMAND_NAME,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
    public function testCreateWithIndexEnabled()
    {
        $this->indexManager->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->indexManager->shouldReceive('create')->once();

        $this->commandTester->execute([
            'command' => CreateCommand::COMMAND_NAME,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Done./',
            $output
        );
    }
}
