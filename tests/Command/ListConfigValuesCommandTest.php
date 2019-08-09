<?php
namespace App\Tests\Command;

use App\Command\ListConfigValuesCommand;
use App\Entity\ApplicationConfig;
use App\Entity\Manager\ApplicationConfigManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ListConfigValuesCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:list-config-values';
    
    protected $commandTester;
    protected $applicationConfigManager;
    
    public function setUp()
    {
        $this->applicationConfigManager = m::mock(ApplicationConfigManager::class);
        $command = new ListConfigValuesCommand(
            $this->applicationConfigManager,
            'TESTING123',
            'SECRET',
            'mysql'
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
        $this->assertRegExp(
            '/\sEnvironment\s|\sTESTING123\s/',
            $output
        );
        $this->assertRegExp(
            '/\sKernel Secret\s|\sSECRET\s/',
            $output
        );
        $this->assertRegExp(
            '/\sDatabase URL\s|\smysql\s/',
            $output
        );
    }

    public function testExecuteWithConnectionException()
    {
        $connectionException = m::mock(ConnectionException::class);
        $this->applicationConfigManager->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andThrow($connectionException);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/^Unable to connect to database./',
            $output
        );
        $this->assertRegExp(
            '/\sEnvironment\s|\sTESTING123\s/',
            $output
        );
        $this->assertRegExp(
            '/\sKernel Secret\s|\sSECRET\s/',
            $output
        );
        $this->assertRegExp(
            '/\sDatabase URL\s|\smysql\s/',
            $output
        );
    }
}
