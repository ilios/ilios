<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\HealthCheckCommand;
use App\Monitor\Composer;
use App\Monitor\DatabaseConnection;
use App\Monitor\DeprecatedConfigurationOption;
use App\Monitor\Frontend;
use App\Monitor\IliosFileSystem;
use App\Monitor\Migrations;
use App\Monitor\NoDefaultSecret;
use App\Monitor\PhpConfiguration;
use App\Monitor\PhpExtension;
use App\Monitor\RequiredENV;
use App\Monitor\SecretLength;
use App\Monitor\Timezone;
use App\Service\Config;
use App\Service\IliosFileSystem as Filesystem;
use App\Service\HealthCheckRunner;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\AvailableMigrationsList;
use Doctrine\Migrations\Metadata\ExecutedMigrationsList;
use Doctrine\Migrations\Version\MigrationStatusCalculator;
use Laminas\Diagnostics\Check\ApcFragmentation;
use Laminas\Diagnostics\Check\ApcMemory;
use Laminas\Diagnostics\Check\DirReadable;
use Laminas\Diagnostics\Check\DirWritable;
use Laminas\Diagnostics\Check\PhpVersion;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

#[Group('cli')]
#[CoversClass(HealthCheckCommand::class)]
final class HealthCheckCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected HealthCheckCommand $command;
    protected array $allChecks;
    protected array $minimalChecks;

    public function setUp(): void
    {
        parent::setUp();
        $runner = new HealthCheckRunner();
        $apcFragmentation = new ApcFragmentation(10, 90);
        $apcMemory = new ApcMemory(10, 90);
        $composer = new Composer();
        $platform = m::mock(AbstractPlatform::class);
        $platform->shouldReceive('getDummySelectSQL')->andReturn('whatever');
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform')->andReturn($platform);
        $connection->shouldReceive('fetchOne')->andReturn(false);
        $databaseConnection = new DatabaseConnection($connection);
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->andReturnNull();
        $deprecatedConfigurationOption = new DeprecatedConfigurationOption($config);
        $dirReadable = new DirReadable(__DIR__);
        $dirWritable = new DirWritable(__DIR__);
        $frontend = new Frontend(__DIR__);
        $config2 = m::mock(Config::class);
        $config2->shouldReceive('get')->with('file_system_storage_path')->andReturn(__DIR__);
        $fileSystem = new IliosFileSystem($config2, m::mock(Filesystem::class));
        $noDefaultSecret = new NoDefaultSecret();
        $statusCalculator = m::mock(MigrationStatusCalculator::class);
        $statusCalculator->shouldReceive('getExecutedUnavailableMigrations')->andReturn(new ExecutedMigrationsList([]));
        $statusCalculator->shouldReceive('getNewMigrations')->andReturn(new AvailableMigrationsList([]));
        $factory = m::mock(DependencyFactory::class);
        $factory->shouldReceive('getMigrationStatusCalculator')->andReturn($statusCalculator);
        $migrations = new Migrations($factory);
        $phpConfiguration = new PhpConfiguration();
        $phpExtension = new PhpExtension(['curl']);
        $phpVersion = new PhpVersion('8');
        $requiredEnv = new RequiredENV();
        $secretLength = new SecretLength();
        $timezone = new Timezone();
        $command = new HealthCheckCommand(
            $runner,
            $apcFragmentation,
            $apcMemory,
            $composer,
            $databaseConnection,
            $deprecatedConfigurationOption,
            $dirReadable,
            $dirWritable,
            $frontend,
            $fileSystem,
            $noDefaultSecret,
            $migrations,
            $phpConfiguration,
            $phpExtension,
            $phpVersion,
            $requiredEnv,
            $secretLength,
            $timezone
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $command = $application->find($command->getName());
        $this->commandTester = new CommandTester($command);
        $this->allChecks = [
            $apcFragmentation,
            $apcMemory,
            $composer,
            $databaseConnection,
            $deprecatedConfigurationOption,
            $dirReadable,
            $dirWritable,
            $frontend,
            $fileSystem,
            $noDefaultSecret,
            $migrations,
            $phpConfiguration,
            $phpExtension,
            $phpVersion,
            $requiredEnv,
            $secretLength,
            $timezone,
        ];
        $this->minimalChecks = [
            $deprecatedConfigurationOption,
            $dirReadable,
            $dirWritable,
            $phpExtension,
            $phpVersion,
            $noDefaultSecret,
            $requiredEnv,
            $secretLength,
            $timezone,
        ];
    }

    public function tearDown(): void
    {
        unset($this->commandTester);
        unset($this->allChecks);
        unset($this->minimalChecks);
        parent::tearDown();
    }

    public function testExecute(): void
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Health Checks (17)', $output);
        $this->assertMatchesRegularExpression('/\sCheck\s|\sStatus\s\s/', $output, 'check table headers');
        $this->assertMatchesRegularExpression('/Summary Status: (OK|KO)/', $output, 'check summary status');
        foreach ($this->allChecks as $check) {
            $this->assertStringContainsString(get_class($check), $output);
        }
    }

    public function testExecuteMinimalChecks(): void
    {
        $this->commandTester->execute(['--minimal' => true]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Health Checks (9)', $output);
        foreach ($this->minimalChecks as $check) {
            $this->assertStringContainsString(get_class($check), $output);
        }
    }
}
