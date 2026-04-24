<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\UpdateFrontendCommand;
use App\Service\Archive;
use App\Service\Config;
use App\Service\Fetch;
use App\Service\Filesystem;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[Group('cli')]
final class UpdateFrontendCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $fetch;
    protected m\MockInterface $fs;
    protected m\MockInterface $config;
    protected m\MockInterface $archive;
    protected TestableUpdateFrontendCommand $command;
    protected CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fetch = m::mock(Fetch::class);
        $this->fs = m::mock(Filesystem::class);
        $this->config = m::mock(Config::class);
        $this->archive = m::mock(Archive::class);

        $this->fs->shouldReceive('mkdir')->andReturnNull();

        $this->command = new TestableUpdateFrontendCommand(
            $this->fetch,
            $this->fs,
            $this->config,
            $this->archive,
            '/tmp',
            'v1',
            'prod'
        );

        $this->commandTester = new CommandTester($this->command);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->fetch, $this->fs, $this->config, $this->archive, $this->commandTester);
    }

    public function testSuccessfulUpdate(): void
    {
        $this->command->setTestData(
            currentVersion: 'v1',
            distributions: ['/tmp/dist/v1']
        );

        $exitCode = $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'Frontend updated successfully!',
            $this->commandTester->getDisplay()
        );
    }

    public function testFailureWhenVersionNotFound(): void
    {
        $this->command->setTestData(
            currentVersion: 'v1',
            distributions: ['/tmp/dist/v2']
        );

        $exitCode = $this->commandTester->execute([
            '--at-version' => 'v1',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString(
            'No matching frontend found',
            $this->commandTester->getDisplay()
        );
    }

    public function testStagingBuild(): void
    {
        $this->command->setTestData(
            currentVersion: null,
            distributions: []
        );

        $exitCode = $this->commandTester->execute([
            '--staging-build' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'Frontend updated successfully from staging build!',
            $this->commandTester->getDisplay()
        );
    }
}

class TestableUpdateFrontendCommand extends UpdateFrontendCommand
{
    private ?string $currentVersion = null;
    private array $distributions = [];

    public function setTestData(?string $currentVersion, array $distributions): void
    {
        $this->currentVersion = $currentVersion;
        $this->distributions = $distributions;
    }

    protected function downloadAndExtractAllArchives(string $environment): ?string
    {
        return $this->currentVersion;
    }

    protected function listDistributions(string $environment): array
    {
        return $this->distributions;
    }

    protected function copyAssetsIntoPublicDirectory(string $distributionPath): void
    {
        // no-op
    }

    protected function activateVersion(string $distributionPath): void
    {
        // no-op
    }
}