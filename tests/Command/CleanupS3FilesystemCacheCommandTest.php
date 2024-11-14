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
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class CleanupS3FilesystemCacheCommandTest
 * @package App\Tests\Command
 */
#[\PHPUnit\Framework\Attributes\Group('cli')]
class CleanupS3FilesystemCacheCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    private const string CACHE_DIR = __DIR__ . '/test';

    protected CommandTester $commandTester;
    protected m\MockInterface $filesystem;
    protected m\MockInterface $diskSpace;

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
        $commandInApp = $application->find($command->getName());
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

    public function testPlentyOfFreeSpaceDoesNothing(): void
    {
        $this->diskSpace->shouldReceive('freeSpace')->once()->with(self::CACHE_DIR)->andReturn(80);
        $this->diskSpace->shouldReceive('totalSpace')->once()->with(self::CACHE_DIR)->andReturn(100);


        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/80% free space. Not cleaning up any files./',
            $output
        );
    }

    public function testDoesCleanup(): void
    {
        $this->diskSpace->shouldReceive('freeSpace')->twice()->with(self::CACHE_DIR)->andReturn(10);
        $this->diskSpace->shouldReceive('totalSpace')->twice()->with(self::CACHE_DIR)->andReturn(100);

        $attr1 = m::mock(StorageAttributes::class);
        $attr1->shouldReceive('type')->andReturn(StorageAttributes::TYPE_DIRECTORY);
        $attr1->shouldNotReceive('path');
        $attr1->shouldReceive('lastModified')->andReturn(time());

        $attr2 = m::mock(StorageAttributes::class);
        $attr2->shouldReceive('type')->andReturn(StorageAttributes::TYPE_DIRECTORY);
        $attr2->shouldNotReceive('path');
        $attr2->shouldReceive('lastModified')->andReturn(strtotime('1 year ago'));

        $attr3 = m::mock(StorageAttributes::class);
        $attr3->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE);
        $attr3->shouldNotReceive('path');
        $attr3->shouldReceive('lastModified')->andReturn(time());

        $attr4 = m::mock(StorageAttributes::class);
        $attr4->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE);
        $attr4->shouldReceive('path')->andReturn('file1');
        $attr4->shouldReceive('lastModified')->andReturn(strtotime('3 days ago'));

        $attr5 = m::mock(StorageAttributes::class);
        $attr5->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE);
        $attr5->shouldNotReceive('path');
        $attr5->shouldReceive('lastModified')->andReturn(strtotime('1 days ago'));

        $attr6 = m::mock(StorageAttributes::class);
        $attr6->shouldReceive('type')->andReturn(StorageAttributes::TYPE_FILE);
        $attr6->shouldReceive('path')->andReturn('file3');
        $attr6->shouldReceive('lastModified')->andReturn(strtotime('1 week ago'));

        $this->filesystem->shouldReceive('listContents')
            ->with(IliosFileSystem::HASHED_LM_DIRECTORY, true)
            ->andReturn(new DirectoryListing([$attr1, $attr2, $attr3, $attr4, $attr5, $attr6]));
        $this->filesystem->shouldReceive('delete')->with('file1');
        $this->filesystem->shouldReceive('delete')->with('file3');

        $this->commandTester->execute([]);

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
