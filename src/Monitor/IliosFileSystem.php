<?php

namespace App\Monitor;

use App\Service\Config;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

class IliosFileSystem implements CheckInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
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

        return new Success('is writable and has enough free space');
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Ilios File System';
    }
}
