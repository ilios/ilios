<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\DiskSpace;
use App\Command\CleanupS3FilesystemCacheCommand;
use App\Service\FilesystemFactory;
use App\Service\IliosFileSystem;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class CleanupS3FilesystemCacheCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class CleanupS3FilesystemCacheCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:cleanup-s3-cache';
    private const CACHE_DIR = __DIR__ . '/test';

    protected CommandTester $commandTester;
    protected FilesystemOperator|m\MockInterface $filesystem;
    protected m\MockInterface|DiskSpace $diskSpace;

    public function setUp(): void
    {
        parent::setUp();
        $factory = m::mock(FilesystemFactory::class);
        $this->filesystem = m::mock(FilesystemOperator::class);
        $this->diskSpace = m::mock(DiskSpace::class);
        $factory->shouldReceive('getS3LocalFilesystemCache')->once()->andReturn($this->filesystem);
        $factory->shouldReceive('getLocalS3CacheDirectory')->once()->andReturn(self::CACHE_DIR);

        $command = new CleanupS3FilesystemCacheCommand($factory, $this->diskSpace);
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
        unset($this->filesystem);
        unset($this->diskSpace);
        unset($this->commandTester);
    }

    public function testPlentyOfFreeSpaceDoesNothing()
    {
        $this->diskSpace->shouldReceive('freeSpace')->once()->with(self::CACHE_DIR)->andReturn(80);
        $this->diskSpace->shouldReceive('totalSpace')->once()->with(self::CACHE_DIR)->andReturn(100);


        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/80% free space. Not cleaning up any files./',
            $output
        );
    }

    public function testDoesCleanup()
    {
        $this->diskSpace->shouldReceive('freeSpace')->twice()->with(self::CACHE_DIR)->andReturn(10);
        $this->diskSpace->shouldReceive('totalSpace')->twice()->with(self::CACHE_DIR)->andReturn(100);

        $this->filesystem->shouldReceive('listContents')
            ->with(IliosFileSystem::HASHED_LM_DIRECTORY, true)
            ->andReturn(new DirectoryListing([
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_DIRECTORY)
                    ->shouldNotReceive('path')
                    ->shouldReceive('lastModified')->andReturn(time())
                    ->mock(),
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_DIRECTORY)
                    ->shouldNotReceive('path')
                    ->shouldReceive('lastModified')->andReturn(strtotime('1 year ago'))
                    ->mock(),
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE)
                    ->shouldNotReceive('path')
                    ->shouldReceive('lastModified')->andReturn(time())
                    ->mock(),
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE)
                    ->shouldReceive('path')->andReturn('file1')
                    ->shouldReceive('lastModified')->andReturn(strtotime('3 days ago'))
                    ->mock(),
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE)
                    ->shouldNotReceive('path')
                    ->shouldReceive('lastModified')->andReturn(strtotime('1 days ago'))
                    ->mock(),
                m::mock(StorageAttributes::class)
                    ->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE)
                    ->shouldReceive('path')->andReturn('file3')
                    ->shouldReceive('lastModified')->andReturn(strtotime('1 week ago'))
                    ->mock(),
            ]));
        $this->filesystem->shouldReceive('delete')->with('file1');
        $this->filesystem->shouldReceive('delete')->with('file3');

        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertMatchesRegularExpression(
            '/10% free space. Will cleanup old files.../',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/2 file\(s\) cleaned up/',
            $output
        );
    }
}
