<?php

namespace App\Monitor;

use App\Exception\IliosFilesystemException;
use App\Service\Config;
use App\Service\IliosFileSystem as Filesystem;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

class IliosFileSystem implements CheckInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Config $config,
        Filesystem $filesystem
    ) {
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function check()
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

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'Ilios File System';
    }
}
