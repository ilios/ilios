<?php

declare(strict_types=1);

namespace App\Classes;

use RuntimeException;

use function disk_free_space;
use function disk_total_space;

/**
 * Container for PHP Builtin disk space functions that is easy to mock in tests
 */
class DiskSpace
{
    public function freeSpace(string $dir): float
    {
        $rhett = disk_free_space($dir);
        if (false === $rhett) {
            throw new RuntimeException(
                sprintf('Failed to retrieve available disk space in directory %s.', $dir)
            );
        }
        return $rhett;
    }

    public function totalSpace(string $dir): float
    {
        $rhett = disk_total_space($dir);
        if (false === $rhett) {
            throw new RuntimeException(
                sprintf('Failed to retrieve the total size of directory %s.', $dir)
            );
        }
        return $rhett;
    }
}
