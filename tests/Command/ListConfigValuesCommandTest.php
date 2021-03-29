<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ListConfigValuesCommand;
use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ListConfigValuesCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class ListConfigValuesCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:list-config-values';

    protected $commandTester;
    protected $applicationConfigRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->applicationConfigRepository = m::mock(ApplicationConfigRepository::class);
        $command = new ListConfigValuesCommand(
            $this->applicationConfigRepository,
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
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->applicationConfigRepository);
        unset($this->commandTester);
    }

    public function testExecute()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('getName')->once()->andReturn('the-name');
        $mockConfig->shouldReceive('getValue')->once()->andReturn('the-value');
        $this->applicationConfigRepository->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andReturn([$mockConfig]);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/\sthe-name\s|\sthe-value\s/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sEnvironment\s|\sTESTING123\s/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sKernel Secret\s|\sSECRET\s/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sDatabase URL\s|\smysql\s/',
            $output
        );
    }

    public function testExecuteWithConnectionException()
    {
        $connectionException = m::mock(ConnectionException::class);
        $this->applicationConfigRepository->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andThrow($connectionException);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/^Unable to connect to database./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sEnvironment\s|\sTESTING123\s/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sKernel Secret\s|\sSECRET\s/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\sDatabase URL\s|\smysql\s/',
            $output
        );
    }
}
