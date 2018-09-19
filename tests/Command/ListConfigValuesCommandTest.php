<?php
namespace Tests\App\Command;

use App\Command\ListConfigValuesCommand;
use App\Entity\ApplicationConfig;
use App\Entity\Manager\ApplicationConfigManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ListConfigValuesCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:list-config-values';
    
    protected $commandTester;
    protected $applicationConfigManager;
    
    public function setUp()
    {
        $this->applicationConfigManager = m::mock(ApplicationConfigManager::class);
        $command = new ListConfigValuesCommand($this->applicationConfigManager);
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
    
    public function testExecute()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('getName')->once()->andReturn('the-name');
        $mockConfig->shouldReceive('getValue')->once()->andReturn('the-value');
        $this->applicationConfigManager->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andReturn([$mockConfig]);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/\sthe-name\s|\sthe-value\s/',
            $output
        );
    }
}
