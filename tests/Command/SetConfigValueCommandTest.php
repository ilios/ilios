<?php
namespace App\Tests\Command;

use App\Command\SetConfigValueCommand;
use App\Entity\ApplicationConfig;
use App\Entity\Manager\ApplicationConfigManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SetConfigValueCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:set-config-value';
    
    protected $commandTester;
    protected $applicationConfigManager;
    
    public function setUp()
    {
        $this->applicationConfigManager = m::mock(ApplicationConfigManager::class);
        $command = new SetConfigValueCommand($this->applicationConfigManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
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
