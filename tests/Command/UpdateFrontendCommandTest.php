<?php
namespace App\Tests\Command;

use App\Command\UpdateFrontendCommand;
use App\Service\Archive;
use App\Service\Config;
use App\Service\Fetch;
use App\Service\Filesystem;
use App\Service\FinderFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;
use Symfony\Component\Finder\Finder;

class UpdateFrontendCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:update-frontend';
    const TEST_API_VERSION = '33.14-test';
    
    protected $commandTester;
    protected $fetch;
    protected $fs;
    protected $config;
    protected $archive;
    protected $finder;
    protected $fakeCacheFileDir;
    protected $fakeProjectFileDir;

    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $testFiles = __DIR__ . '/FakeTestFiles';

        $this->fakeCacheFileDir = $testFiles . '/cache';
        $fs->mkdir($this->fakeCacheFileDir);
        $this->fakeProjectFileDir = $testFiles . '/app';
        $tmpDir = $this->fakeProjectFileDir . '/var/tmp';
        $fs->mkdir($tmpDir);

        $this->fetch = m::mock(Fetch::class);
        $this->fs = m::mock(Filesystem::class);
        $this->config = m::mock(Config::class);
        $this->archive = m::mock(Archive::class);
        $this->fs->shouldReceive('mkdir')->times(3)->andReturn(true);
        $this->finderFactory = m::mock(FinderFactory::class);
        $this->finder = m::mock(Finder::class);
        $command = new UpdateFrontendCommand(
            $this->fetch,
            $this->fs,
            $this->config,
            $this->archive,
            $this->fakeCacheFileDir,
            $this->fakeProjectFileDir,
            self::TEST_API_VERSION,
            'prod'
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
    public function tearDown()
    {
        $fs = new SymfonyFileSystem();
        $fs->remove($this->fakeCacheFileDir);
        $fs->remove($this->fakeProjectFileDir);

        unset($this->fetch);
        unset($this->fs);
        unset($this->config);
        unset($this->archive);
        unset($this->finder);
    }
    
    public function testExecute()
    {
        $fileName = self::TEST_API_VERSION . '/' . UpdateFrontendCommand::ARCHIVE_FILE_NAME;
        $this->fetch->shouldReceive('get')->with(UpdateFrontendCommand::PRODUCTION_CDN_ASSET_DOMAIN . $fileName, null)
            ->once()->andReturn('ARCHIVE_FILE');

        $archiveDir = $this->fakeProjectFileDir  . '/var/tmp/frontend-update-files/prod';
        $parts = [
            $archiveDir,
            self::TEST_API_VERSION,
            'active',
            UpdateFrontendCommand::ARCHIVE_FILE_NAME
        ];
        $archivePath = join(DIRECTORY_SEPARATOR, $parts);

        $this->fs->shouldReceive('dumpFile')->once()->with($archivePath, 'ARCHIVE_FILE');
        $this->archive->shouldReceive('extract')->once()->with($archivePath, $archiveDir);

        $frontendPath = $this->fakeCacheFileDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fs->shouldReceive('remove')->once()->with($frontendPath);
        $this->fs->shouldReceive('rename')->once()
            ->with($archiveDir . UpdateFrontendCommand::UNPACKED_DIRECTORY, $frontendPath);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
        ));

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully!/',
            $output
        );
    }

    public function testExecuteStagingBuild()
    {
        $fileName = self::TEST_API_VERSION . '/' . UpdateFrontendCommand::ARCHIVE_FILE_NAME;
        $this->fetch->shouldReceive('get')->with(UpdateFrontendCommand::STAGING_CDN_ASSET_DOMAIN . $fileName, null)
            ->once()->andReturn('ARCHIVE_FILE');

        $archiveDir = $this->fakeProjectFileDir  . '/var/tmp/frontend-update-files/stage';
        $parts = [
            $archiveDir,
            self::TEST_API_VERSION,
            'active',
            UpdateFrontendCommand::ARCHIVE_FILE_NAME
        ];
        $archivePath = join(DIRECTORY_SEPARATOR, $parts);

        $this->fs->shouldReceive('dumpFile')->once()->with($archivePath, 'ARCHIVE_FILE');
        $this->archive->shouldReceive('extract')->once()->with($archivePath, $archiveDir);

        $frontendPath = $this->fakeCacheFileDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fs->shouldReceive('remove')->once()->with($frontendPath);
        $this->fs->shouldReceive('rename')->once()
            ->with($archiveDir . UpdateFrontendCommand::UNPACKED_DIRECTORY, $frontendPath);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--staging-build'         => true
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully from staging build!/',
            $output
        );
    }

    public function testExecuteVersionBuild()
    {
        $fileName = self::TEST_API_VERSION . '/' . UpdateFrontendCommand::ARCHIVE_FILE_NAME . ':foo.bar';
        $this->fetch->shouldReceive('get')->with(UpdateFrontendCommand::PRODUCTION_CDN_ASSET_DOMAIN . $fileName, null)
            ->once()->andReturn('ARCHIVE_FILE');

        $archiveDir = $this->fakeProjectFileDir  . '/var/tmp/frontend-update-files/prod';
        $parts = [
            $archiveDir,
            self::TEST_API_VERSION,
            'foo.bar',
            UpdateFrontendCommand::ARCHIVE_FILE_NAME
        ];
        $archivePath = join(DIRECTORY_SEPARATOR, $parts);

        $this->fs->shouldReceive('dumpFile')->once()->with($archivePath, 'ARCHIVE_FILE');
        $this->archive->shouldReceive('extract')->once()->with($archivePath, $archiveDir);

        $frontendPath = $this->fakeCacheFileDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $this->fs->shouldReceive('remove')->once()->with($frontendPath);
        $this->fs->shouldReceive('rename')->once()
            ->with($archiveDir . UpdateFrontendCommand::UNPACKED_DIRECTORY, $frontendPath);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--at-version'         => 'foo.bar'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully to version foo.bar!/',
            $output
        );
    }
}
