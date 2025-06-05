<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Entity\LearningMaterialInterface;
use App\Service\FilesystemFactory;
use App\Service\NonCachingIliosFileSystem;
use League\Flysystem\Filesystem;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\IliosFileSystem;
use App\Tests\TestCase;

final class NonCachingIliosFileSystemTest extends TestCase
{
    private NonCachingIliosFileSystem $iliosFileSystem;
    private m\MockInterface $fileSystem;
    private string $fakeTestFileDir;
    private m\MockInterface $fileSystemFactory;

    public function setUp(): void
    {
        parent::setUp();
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->fileSystem = m::mock(Filesystem::class);
        $this->fileSystemFactory = m::mock(FilesystemFactory::class);
        $this->fileSystemFactory->shouldReceive('getNonCachingFilesystem')->once()->andReturn($this->fileSystem);
        $this->iliosFileSystem = new NonCachingIliosFileSystem($this->fileSystemFactory);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->fileSystem);
        unset($this->iliosFileSystem);
        unset($this->fakeTestFileDir);
        unset($this->fileSystemFactory);
    }

    public function testStoreLeaningMaterialFile(): void
    {
        $path = __FILE__;
        $file = m::mock(File::class);
        $file->shouldReceive('getPathname')->andReturn($path);
        $this->fileSystem->shouldReceive('writeStream');
        $this->iliosFileSystem->storeLearningMaterialFile($file);
    }

    public function testGetLearningMaterialFilePath(): void
    {
        $path = __FILE__;
        $file = m::mock(File::class);
        $file->shouldReceive('getPathname')->andReturn($path);
        $newPath = $this->iliosFileSystem->getLearningMaterialFilePath($file);
        $this->assertSame($this->fakeTestFileDir . '/' . $newPath, $this->getTestFilePath($path));
    }

    public function testRemoveFile(): void
    {
        $file = 'foojunk';
        $this->fileSystem->shouldReceive('delete')->with($file);
        $this->iliosFileSystem->removeFile($file);
    }

    public function testGetFileContentsOnNonCachingFileSystem(): void
    {
        $filename = 'test/file/name';
        $value = 'something something word word';
        $this->fileSystem->shouldReceive('fileExists')->with($filename)->once()->andReturn(true);
        $this->fileSystem->shouldReceive('read')->with($filename)->once()->andReturn($value);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertEquals($value, $result);
    }

    public function testGetFileContents(): void
    {
        $fileSystem = m::mock(LocalCachingFilesystemDecorator::class);
        $iliosFileSystem = new IliosFileSystem($fileSystem);
        $filename = 'test/file/name';
        $value = 'something something word word';
        $fileSystem->shouldReceive('fileExists')->with($filename)->once()->andReturn(true);
        $fileSystem->shouldReceive('read')->with($filename)->once()->andReturn($value);
        $result = $iliosFileSystem->getFileContents($filename);
        $this->assertEquals($value, $result);
    }

    public function testMissingGetFileContents(): void
    {
        $filename = 'test/file/name';
        $this->fileSystem->shouldReceive('fileExists')->with($filename)->once()->andReturn(false);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertFalse($result);
    }

    public function testCheckLearningMaterialFilePath(): void
    {
        $goodLm = m::mock(LearningMaterialInterface::class);
        $goodLm->shouldReceive('getRelativePath')->andReturn('goodfile');
        $badLm = m::mock(LearningMaterialInterface::class);
        $badLm->shouldReceive('getRelativePath')->andReturn('badfile');

        $this->fileSystem->shouldReceive('fileExists')
            ->with('goodfile')->andReturn(true)->once();
        $this->fileSystem->shouldReceive('fileExists')
            ->with('badfile')->andReturn(false)->once();
        $this->assertTrue($this->iliosFileSystem->checkLearningMaterialFilePath($goodLm));
        $this->assertFalse($this->iliosFileSystem->checkLearningMaterialFilePath($badLm));
    }

    protected function getTestFilePath(string $path): string
    {
        $hash = md5_file($path);
        $hashDirectory = substr($hash, 0, 2);
        $parts = [
            $this->fakeTestFileDir,
            'learning_materials',
            'lm',
            $hashDirectory,
            $hash,
        ];
        return implode('/', $parts);
    }

    protected function getTestFileLock(string $name): string
    {
        $parts = [
            IliosFileSystem::LOCK_FILE_DIRECTORY,
            $name,
        ];
        return implode('/', $parts);
    }

    public function testCreateLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(false);
        $this->fileSystem->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }

    public function testReleaseLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(true);
        $this->fileSystem->shouldReceive('delete')->with($lockFilePath);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testReleaseLockWithNoLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(false);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testHasLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(true);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertTrue($status);
    }

    public function testDoesNotHaveLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(false);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertFalse($status);
    }

    public function testWaitForLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(false);
        $this->fileSystem->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->waitForLock($name);
    }

    public function testConvertsUnsafeFileNames(): void
    {
        $name = 'test && file .lock';
        $lockFilePath = $this->getTestFileLock('test-file-.lock');
        $this->fileSystem->shouldReceive('fileExists')->with($lockFilePath)->andReturn(true);
        $this->fileSystem->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }
}
