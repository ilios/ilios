<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Tests\TestCase;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use Mockery as m;

/**
 * Class LocalCachingFilesystemDecoratorTest
 * @package App\Tests\Classes
 */
final class LocalCachingFilesystemDecoratorTest extends TestCase
{
    private FilesystemOperator|m\MockInterface $cacheFileSystem;
    private FilesystemOperator|m\MockInterface $remoteFileSystem;
    private LocalCachingFilesystemDecorator $subject;

    protected function setUp(): void
    {
        $this->cacheFileSystem = m::mock(FilesystemOperator::class);
        $this->remoteFileSystem = m::mock(FilesystemOperator::class);
        $this->subject = new LocalCachingFilesystemDecorator(
            $this->cacheFileSystem,
            $this->remoteFileSystem
        );
    }

    protected function tearDown(): void
    {
        unset($this->cacheFileSystem);
        unset($this->remoteFileSystem);
        unset($this->subject);
    }

    public function testReadDoesNotCacheFailures(): void
    {
        $path = __FILE__;
        $this->remoteFileSystem->shouldReceive('read')->with($path)->andThrow(UnableToReadFile::class);
        $this->cacheFileSystem->shouldReceive('fileExists')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldNotReceive('write');
        $this->expectException(UnableToReadFile::class);

        $result = $this->subject->read($path);
        $this->assertFalse($result);
    }

    public function testReadStreamDoesNotCacheFailures(): void
    {
        $path = __FILE__;
        $this->remoteFileSystem->shouldReceive('readStream')->with($path)->andThrow(UnableToReadFile::class);
        $this->cacheFileSystem->shouldReceive('fileExists')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldNotReceive('writeStream');
        $this->expectException(UnableToReadFile::class);

        $result = $this->subject->readStream($path);
        $this->assertFalse($result);
    }
}
