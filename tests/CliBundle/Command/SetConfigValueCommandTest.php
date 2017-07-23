<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\SetConfigValueCommand;
use Ilios\CoreBundle\Entity\ApplicationConfig;
use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SetConfigValueCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:set-config-value';
    
    protected $commandTester;
    protected $applicationConfigManager;
    
    public function setUp()
    {
        $this->applicationConfigManager = m::mock(ApplicationConfigManager::class);
        $command = new SetConfigValueCommand($this->applicationConfigManager);
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
        unset($this->applicationConfigManager);
        unset($this->commandTester);
    }
    
    public function testSaveExistingConfig()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $this->applicationConfigManager->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
            'value'        => 'bar',
        ));
    }

    public function testSaveNewConfig()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $mockConfig->shouldReceive('setName')->with('foo')->once();
        $this->applicationConfigManager->shouldReceive('findOneBy')->with(['name' => 'foo'])->once()->andReturn(null);
        $this->applicationConfigManager->shouldReceive('create')->once()->andReturn($mockConfig);
        $this->applicationConfigManager->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
            'value'        => 'bar',
        ));
    }
    
    public function testNameRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'value'        => 'bar',
        ));
    }

    public function testValueRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
        ));
    }
}
