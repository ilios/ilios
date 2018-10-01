<?php
namespace App\Tests\Command;

use App\Command\SetSchoolConfigValueCommand;
use App\Entity\Manager\SchoolManager;
use App\Entity\SchoolConfig;
use App\Entity\Manager\SchoolConfigManager;
use App\Entity\SchoolInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class SetSchoolConfigValueCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:set-school-config-value';
    
    protected $commandTester;
    protected $schoolManager;
    protected $schoolConfigManager;

    public function setUp()
    {
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->schoolConfigManager = m::mock(SchoolConfigManager::class);
        $command = new SetSchoolConfigValueCommand($this->schoolManager, $this->schoolConfigManager);
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
        unset($this->schoolManager);
        unset($this->schoolConfigManager);
        unset($this->commandTester);
    }
    
    public function testSaveExistingConfig()
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $mockSchool->shouldReceive('getId')->once()->andReturn(1);
        $this->schoolManager->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $this->schoolConfigManager->shouldReceive('findOneBy')
            ->with(['school' => '1', 'name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->schoolConfigManager->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'school'         => '1',
            'name'         => 'foo',
            'value'        => 'bar',
        ));
    }

    public function testSaveNewConfig()
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $mockSchool->shouldReceive('getId')->once()->andReturn(1);
        $this->schoolManager->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $mockConfig->shouldReceive('setSchool')->with($mockSchool)->once();
        $mockConfig->shouldReceive('setName')->with('foo')->once();
        $this->schoolConfigManager
            ->shouldReceive('findOneBy')
            ->with(['school' => '1', 'name' => 'foo'])
            ->once()
            ->andReturn(null);
        $this->schoolConfigManager->shouldReceive('create')->once()->andReturn($mockConfig);
        $this->schoolConfigManager->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            'school' => '1',
            'name' => 'foo',
            'value' => 'bar',
        ));
    }
    
    public function testNameRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'school'         => '1',
            'value'        => 'bar',
        ));
    }

    public function testValueRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'school'         => '1',
            'name'         => 'foo',
        ));
    }

    public function testSchoolRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
            'value'         => 'bar',
        ));
    }
}
