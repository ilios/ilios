<?php

namespace App\Classes;

/**
 * Container for PHP Builtin disk space functions that is easy to mock in tests
 */
class DiskSpace
{
    public function freeSpace($dir) : float
    {
        return \disk_free_space($dir);
    }

    public function totalSpace($dir) : float
    {
        return \disk_total_space($dir);
    }
}
