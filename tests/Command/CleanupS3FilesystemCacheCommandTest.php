<?php
namespace App\Tests\Command;

use App\Classes\DiskSpace;
use App\Command\CleanupS3FilesystemCacheCommand;
use App\Service\FilesystemFactory;
use App\Service\IliosFileSystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class CleanupS3FilesystemCacheCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:cleanup-s3-cache';
    const CACHE_DIR = __DIR__ . '/test';

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    protected $filesystem;

    /** @var m\Mock */
    protected $diskSpace;


    public function setUp()
    {
        $factory = m::mock(FilesystemFactory::class);
        $this->filesystem = m::mock(FilesystemInterface::class);
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
    public function tearDown()
    {
        unset($this->filesystem);
        unset($this->diskSpace);
        unset($this->commandTester);
    }
    
    public function testPlentyOfFreeSpaceDoesNothing()
    {
        $this->diskSpace->shouldReceive('freeSpace')->once()->with(self::CACHE_DIR)->andReturn(80);
        $this->diskSpace->shouldReceive('totalSpace')->once()->with(self::CACHE_DIR)->andReturn(100);


        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
        ));

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
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
            ->andReturn([
                ['type' => 'dir', 'path' => 'dir0', 'timestamp' => time()],
                ['type' => 'dir', 'path' => 'dir0', 'timestamp' => strtotime('1 year ago')],
                ['type' => 'file', 'path' => 'file0', 'timestamp' => time()],
                ['type' => 'file', 'path' => 'file1', 'timestamp' => strtotime('3 days ago')],
                ['type' => 'file', 'path' => 'file2', 'timestamp' => strtotime('1 day ago')],
                ['type' => 'file', 'path' => 'file3', 'timestamp' => strtotime('1 week ago')],
            ]);
        $this->filesystem->shouldReceive('delete')->with('file1');
        $this->filesystem->shouldReceive('delete')->with('file3');

        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
        ));

        $output = $this->commandTester->getDisplay();

        $this->assertRegExp(
            '/10% free space. Will cleanup old files.../',
            $output
        );
        $this->assertRegExp(
            '/2 file\(s\) cleaned up/',
            $output
        );
    }
}
