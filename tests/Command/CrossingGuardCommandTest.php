<?php
namespace App\Tests\Command;

use App\Command\CrossingGuardCommand;
use App\Service\CrossingGuard;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class CrossingGuardCommandTest
 * @group cli
 */
class CrossingGuardCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:crossing-guard';

    protected $crossingGuard;
    protected $commandTester;

    public function setUp()
    {
        $this->crossingGuard = m::mock(CrossingGuard::class);

        $command = new CrossingGuardCommand(
            $this->crossingGuard
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown() : void
    {
        unset($this->crossingGuard);
        unset($this->commandTester);
    }

    public function testEnable()
    {
        $this->crossingGuard->shouldReceive('enable')->once();
        $this->crossingGuard->shouldReceive('isStopped')->once()->andReturn(true);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'action'         => 'down'
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            CrossingGuardCommand::ENABLED_MESSAGE,
            trim($output)
        );
    }

    public function testDisable()
    {
        $this->crossingGuard->shouldReceive('disable')->once();
        $this->crossingGuard->shouldReceive('isStopped')->once()->andReturn(false);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'action'         => 'up'
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            CrossingGuardCommand::DISABLED_MESSAGE,
            trim($output)
        );
    }

    public function testStatusEnabled()
    {
        $this->crossingGuard->shouldReceive('isStopped')->once()->andReturn(true);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'action'         => 'status'
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            CrossingGuardCommand::ENABLED_MESSAGE,
            trim($output)
        );
    }

    public function testStatusDisabled()
    {
        $this->crossingGuard->shouldReceive('isStopped')->once()->andReturn(false);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'action'         => 'status'
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            CrossingGuardCommand::DISABLED_MESSAGE,
            trim($output)
        );
    }
}
