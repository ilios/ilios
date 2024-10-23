<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\LearningMaterialInterface;
use League\Flysystem\Filesystem;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\IliosFileSystem;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IliosFileSystemTest extends TestCase
{
    private IliosFileSystem $iliosFileSystem;
    private m\MockInterface $fileSystemMock;
    private string $fakeTestFileDir;

    public function setUp(): void
    {
        parent::setUp();
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->fileSystemMock = m::mock(Filesystem::class);
        $this->iliosFileSystem = new IliosFileSystem($this->fileSystemMock);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->fileSystemMock);
        unset($this->iliosFileSystem);
        unset($this->fakeTestFileDir);
    }

    public function testStoreLeaningMaterialFile(): void
    {
        $path = __FILE__;
        $file = m::mock(File::class);
        $file->shouldReceive('getPathname')->andReturn($path)->twice();
        $this->fileSystemMock->shouldReceive('writeStream');
        $this->iliosFileSystem->storeLearningMaterialFile($file);
    }

    public function testGetLearningMaterialFilePath(): void
    {
        $path = __FILE__;
        $file = m::mock(File::class);
        $file->shouldReceive('getPathname')->andReturn($path)->once();
        $newPath = $this->iliosFileSystem->getLearningMaterialFilePath($file);
        $this->assertSame($this->fakeTestFileDir . '/' . $newPath, $this->getTestFilePath($path));
    }

    public function testRemoveFile(): void
    {
        $file = 'foojunk';
        $this->fileSystemMock->shouldReceive('delete')->once()->with($file);
        $this->iliosFileSystem->removeFile($file);
    }

    public function testGetFileContents(): void
    {
        $filename = 'test/file/name';
        $value = 'something something word word';
        $this->fileSystemMock->shouldReceive('fileExists')->with($filename)->once()->andReturn(true);
        $this->fileSystemMock->shouldReceive('read')->with($filename)->once()->andReturn($value);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertEquals($value, $result);
    }

    public function testMissingGetFileContents(): void
    {
        $filename = 'test/file/name';
        $this->fileSystemMock->shouldReceive('fileExists')->with($filename)->once()->andReturn(false);
        $result = $this->iliosFileSystem->getFileContents($filename);
        $this->assertFalse($result);
    }

    public function testCheckLearningMaterialFilePath(): void
    {
        $goodLm = m::mock(LearningMaterialInterface::class);
        $goodLm->shouldReceive('getRelativePath')->andReturn('goodfile');

        $badLm = m::mock(LearningMaterialInterface::class);
        $badLm->shouldReceive('getRelativePath')->andReturn('badfile');

        $this->fileSystemMock->shouldReceive('fileExists')
            ->with('goodfile')->andReturn(true)->once();
        $this->fileSystemMock->shouldReceive('fileExists')
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
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->twice()->andReturn(false);
        $this->fileSystemMock->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }

    public function testReleaseLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->twice()->andReturn(true);
        $this->fileSystemMock->shouldReceive('delete')->with($lockFilePath);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testReleaseLockWithNoLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->once()->andReturn(false);
        $this->iliosFileSystem->releaseLock($name);
    }

    public function testHasLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->andReturn(true);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertTrue($status);
    }

    public function testDoesNotHaveLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->andReturn(false);
        $status = $this->iliosFileSystem->hasLock($name);
        $this->assertFalse($status);
    }

    public function testWaitForLock(): void
    {
        $name = 'test.lock';
        $lockFilePath = $this->getTestFileLock($name);
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->times(3)->andReturn(false);
        $this->fileSystemMock->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->waitForLock($name);
    }

    public function testConvertsUnsafeFileNames(): void
    {
        $name = 'test && file .lock';
        $lockFilePath = $this->getTestFileLock('test-file-.lock');
        $this->fileSystemMock->shouldReceive('fileExists')->with($lockFilePath)->once()->andReturn(true);
        $this->fileSystemMock->shouldReceive('write')->with($lockFilePath, 'LOCK');
        $this->iliosFileSystem->createLock($name);
    }

    public function testStoreUploadedTemporaryFile(): void
    {
        $path = __FILE__;
        $file = m::mock(UploadedFile::class);
        $file->shouldReceive('getPathname')->andReturn($path)->twice();
        $this->fileSystemMock->shouldReceive('writeStream');
        $this->iliosFileSystem->storeUploadedTemporaryFile($file);
    }

    public function testGetUploadedTemporaryFileContents(): void
    {
        $hash = md5_file(__FILE__);
        $testContents = file_get_contents(__FILE__);
        $this->fileSystemMock->shouldReceive('fileExists')->with("tmp/{$hash}")->andReturn(true);
        $this->fileSystemMock->shouldReceive('read')->with("tmp/{$hash}")->andReturn($testContents);
        $this->fileSystemMock->shouldReceive('delete')->with("tmp/{$hash}");
        $contents = $this->iliosFileSystem->getUploadedTemporaryFileContentsAndRemoveFile($hash);
        $this->assertSame($contents, $testContents);
    }
}
