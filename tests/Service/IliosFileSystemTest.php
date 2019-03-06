<?php
namespace App\Tests\Service;

use App\Entity\LearningMaterialInterface;
use App\Service\Config;
use League\Flysystem\Filesystem;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use \Symfony\Component\HttpFoundation\File\File;

use App\Service\IliosFileSystem;
use App\Tests\TestCase;

class IliosFileSystemTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    /**
     *
     * @var IliosFileSystem
     */
    private $iliosFileSystem;

    /**
     * @var m\Mock
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $fakeTestFileDir;
    
    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->fileSystem = m::mock(Filesystem::class);
        $this->iliosFileSystem = new IliosFileSystem($this->fileSystem);
    }

    public function tearDown() : void
    {
        unset($this->fileSystem);
        unset($this->iliosFileSystem);
        unset($this->fakeTestFileDir);
    }

    public function testStoreLeaningMaterialFile()
    {
        $path = __FILE__;
        $file = m::mock(File::class)
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $this->fileSystem->shouldReceive('writeStream');
        $this->iliosFileSystem->storeLearningMaterialFile($file);
    }

    public function testGetLearningMaterialFilePath()
    {
        $path = __FILE__;
        $file = m::mock(File::class)
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $newPath = $this->iliosFileSystem->getLearningMaterialFilePath($file);
        $this->assertSame($this->fakeTestFileDir . '/' . $newPath, $this->getTestFilePath($path));
    }

    public function testRemoveFile()
    {
        $file = 'foojunk';
        $this->fileSystem->shouldReceive('delete')->with($file);
        $this->iliosFileSystem->removeFile($file);
    }

    public function testGetFileContents()
    {
        $filename = 'test/file/name';
        $value = 'something something word word';
        $this->fileSystem->shouldReceive('has')->with($filename)->once()->andReturn(true);
        $this->fileSystem->shouldReceive('read')->with($filename)->once()->andReturn($value);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertEquals($value, $result);
    }

    public function testMissingGetFileContents()
    {
        $filename = 'test/file/name';
        $this->fileSystem->shouldReceive('has')->with($filename)->once()->andReturn(false);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertFalse($result);
    }

    public function testCheckLearningMaterialFilePath()
    {
        $goodLm = m::mock(LearningMaterialInterface::class)
            ->shouldReceive('getRelativePath')->andReturn('goodfile')
            ->mock();
        $badLm = m::mock(LearningMaterialInterface::class)
            ->shouldReceive('getRelativePath')->andReturn('badfile')
            ->mock();
        $this->fileSystem->shouldReceive('has')
            ->with('goodfile')->andReturn(true)->once();
        $this->fileSystem->shouldReceive('has')
            ->with('badfile')->andReturn(false)->once();
        $this->assertTrue($this->iliosFileSystem->checkLearningMaterialFilePath($goodLm));
        $this->assertFalse($this->iliosFileSystem->checkLearningMaterialFilePath($badLm));
    }

    protected function getTestFilePath($path)
    {
        $hash = md5_file($path);
        $hashDirectory = substr($hash, 0, 2);
        $parts = [
            $this->fakeTestFileDir,
            'learning_materials',
            'lm',
            $hashDirectory,
            $hash
        ];
        return implode($parts, '/');
    }

    protected function getTestFileLock($name)
    {
        $parts = [
            IliosFileSystem::LOCK_FILE_DIRECTORY,
            $name
        ];
        return implode($parts, '/');
    }

    public function testCreateLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(false);
        $this->fileSystem->shouldReceive('put')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }

    public function testReleaseLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(true);
        $this->fileSystem->shouldReceive('delete')->with($lockFilePath);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testReleaseLockWithNoLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(false);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testHasLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(true);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertTrue($status);
    }

    public function testDoesNotHaveLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(false);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertFalse($status);
    }

    public function testWaitForLock()
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(false);
        $this->fileSystem->shouldReceive('put')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->waitForLock($name);
    }

    public function testConvertsUnsafeFileNames()
    {
        $name = 'test && file .lock';
        $lockFilePath = $this->getTestFileLock('test-file-.lock');
        $this->fileSystem->shouldReceive('has')->with($lockFilePath)->andReturn(true);
        $this->fileSystem->shouldReceive('put')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }
}
