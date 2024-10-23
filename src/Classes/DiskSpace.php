<?php

declare(strict_types=1);

namespace App\Classes;

use function disk_free_space;
use function disk_total_space;

/**
 * Container for PHP Builtin disk space functions that is easy to mock in tests
 */
class DiskSpace
{
    public function freeSpace(string $dir): float
    {
        return disk_free_space($dir);
    }

    public function totalSpace(string $dir): float
    {
        return disk_total_space($dir);
    }
}
