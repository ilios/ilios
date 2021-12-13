<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Tests\TestCase;
use League\Flysystem\FilesystemInterface;
use Mockery as m;

/**
 * Class LocalCachingFilesystemDecoratorTest
 * @package App\Tests\Classes
 */
class LocalCachingFilesystemDecoratorTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    private $cacheFileSystem;

    /**
     * @var m\MockInterface
     */
    private $remoteFileSystem;

    /**
     * @var LocalCachingFilesystemDecorator
     */
    private $subject;

    protected function setUp(): void
    {
        $this->cacheFileSystem = m::mock(FilesystemInterface::class);
        $this->remoteFileSystem = m::mock(FilesystemInterface::class);
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

    public function testReadDoesNotCacheFailures()
    {
        $path = __FILE__;
        $this->remoteFileSystem->shouldReceive('read')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldReceive('has')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldNotReceive('put');

        $result = $this->subject->read($path);
        $this->assertFalse($result);
    }

    public function testReadStreamDoesNotCacheFailures()
    {
        $path = __FILE__;
        $this->remoteFileSystem->shouldReceive('readStream')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldReceive('has')->with($path)->andReturn(false);
        $this->cacheFileSystem->shouldNotReceive('putStream');

        $result = $this->subject->readStream($path);
        $this->assertFalse($result);
    }
}
