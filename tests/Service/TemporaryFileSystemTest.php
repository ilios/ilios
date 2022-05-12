<?php

declare(strict_types=1);

namespace App\Tests\Service;

use Mockery as m;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use App\Service\TemporaryFileSystem;
use App\Tests\TestCase;

class TemporaryFileSystemTest extends TestCase
{
    /**
     *
     * @var TemporaryFileSystem
     */
    private $tempFileSystem;

    /**
     * Mock File System
     * @var SymfonyFileSystem
     */
    private $mockFileSystem;

    /**
     * @var string
     */
    private $uploadDirectory;

    /**
     * @var string
     */
    private $fakeTestFileDir;

    public function setUp(): void
    {
        parent::setUp();
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->uploadDirectory = $this->fakeTestFileDir .  '/var/tmp/uploads';
        $fs->mkdir($this->uploadDirectory);

        $this->mockFileSystem = m::mock(SymfonyFileSystem::class);
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->fakeTestFileDir .  '/var/tmp')->andReturn(true);
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->uploadDirectory)->andReturn(true);

        $this->tempFileSystem = new TemporaryFileSystem($this->mockFileSystem, $this->fakeTestFileDir);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->mockFileSystem);
        unset($this->iliosFileSystem);

        $fs = new SymfonyFileSystem();
        $fs->remove($this->fakeTestFileDir);
    }

    public function testStoreFile()
    {
        $path = __FILE__;
        $hash = md5_file($path);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($path)->getMock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->uploadDirectory . '/' . $hash);
        $this->mockFileSystem->shouldReceive('rename')
            ->with($path, $this->uploadDirectory . '/' . $hash);
        $this->tempFileSystem->storeFile($file);
    }

    public function testRemoveFile()
    {
        $file = 'foojunk';
        $this->mockFileSystem->shouldReceive('remove')->with($this->uploadDirectory . '/' . $file);
        $this->tempFileSystem->removeFile($file);
    }

    public function testGetFile()
    {
        $fs = new SymfonyFileSystem();
        $someJunk = 'whatever dude';
        $hash = md5($someJunk);
        $testFilePath = $this->uploadDirectory . '/' . $hash;
        file_put_contents($testFilePath, $someJunk);
        $file = m::mock('Symfony\Component\HttpFoundation\File\File')
            ->shouldReceive('getPathname')->andReturn($testFilePath)->getMock();
        $this->mockFileSystem->shouldReceive('exists')
            ->with($testFilePath)->andReturn(true);
        $this->mockFileSystem->shouldReceive('move');
        $newHash = $this->tempFileSystem->storeFile($file, false);

        $newFile = $this->tempFileSystem->getFile($newHash);
        $this->assertSame($hash, $newHash);
        $this->assertSame(file_get_contents($newFile->getPathname()), $someJunk);
    }
}
