<?php
namespace App\Tests\Command;

use App\Command\ListSchoolConfigValuesCommand;
use App\Entity\Manager\SchoolConfigManager;
use App\Entity\Manager\SchoolManager;
use App\Entity\SchoolConfigInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ListSchoolConfigValuesCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:list-school-config-values';
    
    protected $commandTester;
    protected $schoolManager;
    protected $schoolConfigManager;

    public function setUp()
    {
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->schoolConfigManager = m::mock(SchoolConfigManager::class);
        $command = new ListSchoolConfigValuesCommand($this->schoolManager, $this->schoolConfigManager);
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
        unset($this->schoolManager);
        unset($this->schoolConfigManager);
        unset($this->commandTester);
    }
    
    public function testExecute()
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $this->schoolManager->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfigInterface::class);
        $mockConfig->shouldReceive('getName')->once()->andReturn('the-name');
        $mockConfig->shouldReceive('getValue')->once()->andReturn('the-value');
        $this->schoolConfigManager->shouldReceive('findBy')
            ->with(['school' => '1'], ['name' => 'asc'])
            ->once()
            ->andReturn([$mockConfig]);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'school'         => '1'
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/\sthe-name\s|\sthe-value\s/',
            $output
        );
    }

    public function testSchoolRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
        ));
    }
}
