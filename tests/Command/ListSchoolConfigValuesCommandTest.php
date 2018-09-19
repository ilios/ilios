<?php
namespace App\Tests\Command;

use App\Command\ListSchoolConfigValuesCommand;
use App\Entity\Manager\SchoolConfigManager;
use App\Entity\Manager\SchoolManager;
use App\Entity\SchoolConfigInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ListSchoolConfigValuesCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:list-school-config-values';
    
    protected $commandTester;
    protected $schoolManager;
    protected $schoolConfigManager;

    public function setUp()
    {
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->schoolConfigManager = m::mock(SchoolConfigManager::class);
        $command = new ListSchoolConfigValuesCommand($this->schoolManager, $this->schoolConfigManager);
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
