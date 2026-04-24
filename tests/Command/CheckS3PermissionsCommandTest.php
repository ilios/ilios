<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CheckS3PermissionsCommand;
use App\Exception\IliosFilesystemException;
use App\Service\Config;
use App\Service\IliosFileSystem;
use Exception;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the Check S3 Permissions command.
 *
 * Class CheckS3PermissionsCommandTest
 */
#[Group('cli')]
#[CoversClass(CheckS3PermissionsCommand::class)]
final class CheckS3PermissionsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $config;
    protected m\MockInterface $iliosFileSystem;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = m::mock(Config::class);
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);

        $command = new CheckS3PermissionsCommand($this->config, $this->iliosFileSystem);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);

        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->config);
        unset($this->iliosFileSystem);
        unset($this->commandTester);
    }

    public function testNoS3Configuration(): void
    {
        $this->config->shouldReceive('get')
            ->with('storage_s3_url')
            ->andReturn(null);

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertSame(
            'No configuration found for storage_s3_url. Nothing to check.',
            trim($this->commandTester->getDisplay())
        );
    }

    public function testCheckS3Permissions(): void
    {
        $this->config->shouldReceive('get')
            ->with('storage_s3_url')
            ->andReturn('s3://example-bucket');

        $this->iliosFileSystem->shouldReceive('testCRUD');

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        $output = trim($this->commandTester->getDisplay());

        $this->assertSame(
            "Connecting to the filesystem and checking permissions.\nAll Systems Go!!",
            $output
        );
    }

    public function testCheckS3PermissionsFailure(): void
    {
        $this->config->shouldReceive('get')
            ->with('storage_s3_url')
            ->andReturn('s3://example-bucket');

        $exception = m::mock(IliosFilesystemException::class);
        $exception->shouldReceive('getMessage')->andReturn('Connecting to the filesystem and checking permissions.');

        $this->iliosFileSystem->shouldReceive('testCRUD')->andThrow($exception);

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);

        $output = trim($this->commandTester->getDisplay());

        $this->assertSame('Connecting to the filesystem and checking permissions.', $output);
    }
}