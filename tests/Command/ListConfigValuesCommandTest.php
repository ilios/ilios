<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\ListConfigValuesCommand;
use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ListConfigValuesCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
class ListConfigValuesCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $applicationConfigRepository;

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
        $commandInApp = $application->find($command->getName());
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

    public function testExecute(): void
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('getName')->once()->andReturn('the-name');
        $mockConfig->shouldReceive('getValue')->once()->andReturn('the-value');
        $this->applicationConfigRepository->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andReturn([$mockConfig]);

        $this->commandTester->execute([]);
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
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithConnectionException(): void
    {
        $exception = m::mock(ConnectionException::class);
        $this->applicationConfigRepository->shouldReceive('findBy')
            ->with([], ['name' => 'asc'])
            ->once()
            ->andThrow($exception);

        $this->commandTester->execute([]);
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
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
