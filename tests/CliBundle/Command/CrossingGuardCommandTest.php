<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\CrossingGuardCommand;
use Ilios\CoreBundle\Service\CrossingGuard;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class CrossingGuardCommandTest
 */
class CrossingGuardCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:crossing-guard';

    protected $crossingGuard;
    protected $commandTester;
    
    public function setUp()
    {
        $this->crossingGuard = m::mock('Ilios\CoreBundle\Service\CrossingGuard');

        $command = new CrossingGuardCommand(
            $this->crossingGuard
        );
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
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
