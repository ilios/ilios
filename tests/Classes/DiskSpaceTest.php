<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\DiskSpace;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DiskSpace::class)]
final class DiskSpaceTest extends TestCase
{
    protected DiskSpace $diskSpace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->diskSpace = new DiskSpace();
    }

    protected function tearDown(): void
    {
        unset($this->diskSpace);
        parent::tearDown();
    }

    public function testFreeSpaceSpace(): void
    {
        $dfs = disk_free_space(__DIR__);
        $this->assertEquals($dfs, $this->diskSpace->freeSpace(__DIR__));
    }

    /**
     * Disabling PHPUnit's error handler for this test
     * in order to suppress the PHP Warning emitted by the method under test.
     */
    #[WithoutErrorHandler()]
    public function testFreeSpaceThrowsException(): void
    {
        // first, grab the current reporting level.
        $prev = error_reporting();
        // then, (temporarily) suppress warnings.
        error_reporting($prev & ~E_WARNING);

        $path = 'not/a/valid/dir';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains("Failed to retrieve available disk space in directory {$path}.");
        $this->diskSpace->freeSpace($path);

        // finally, restore original error reporting level.
        error_reporting($prev);
    }

    public function testTotalSpace(): void
    {
        $dts = disk_total_space(__DIR__);
        $this->assertEquals($dts, $this->diskSpace->totalSpace(__DIR__));
    }

    #[WithoutErrorHandler()]
    public function testTotalThrowsException(): void
    {
        $prev = error_reporting();
        error_reporting($prev & ~E_WARNING);

        $path = 'not/a/valid/dir';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains("Failed to retrieve the total size of directory {$path}.");
        $this->diskSpace->totalSpace($path);

        error_reporting($prev);
    }
}
