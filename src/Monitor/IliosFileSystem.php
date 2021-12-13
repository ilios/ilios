<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Result\ResultInterface;
use App\Exception\IliosFilesystemException;
use App\Service\Config;
use App\Service\IliosFileSystem as Filesystem;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class IliosFileSystem implements CheckInterface
{
    public function __construct(protected Config $config, private Filesystem $filesystem)
    {
    }

    public function check(): ResultInterface
    {
        $path = $this->config->get('file_system_storage_path');
        if (!is_writable($path)) {
            return new Failure("${path} is not writable");
        }

        $freeSpace = disk_free_space($path);
        $totalSpace = disk_total_space($path);
        $usedSpace = $totalSpace - $freeSpace;
        $percentUsed = ($usedSpace / $totalSpace) * 100;
        if ($percentUsed >= 90) {
            return new Failure(sprintf('Disk usage too high: %2d percent.', $percentUsed));
        }
        if ($percentUsed >= 75) {
            return new Warning(sprintf('Disk usage high: %2d percent.', $percentUsed));
        }

        try {
            $this->filesystem->testCRUD();
        } catch (IliosFilesystemException $e) {
            return new Failure("Problem accessing the filesystem " . $e->getMessage());
        }

        return new Success('is writable and has enough free space');
    }

    public function getLabel(): string
    {
        return 'Ilios File System';
    }
}
