<?php

namespace App\Monitor;

use App\Command\UpdateFrontendCommand;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class Frontend implements CheckInterface
{
    private $cacheDir;

    public function __construct($kernelCacheDir)
    {
        $this->cacheDir = $kernelCacheDir;
    }

    /**
     * @inheritdoc
     */
    public function check()
    {
        $assetsPath = $this->cacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $path = $assetsPath . 'index.json';

        if (!file_exists($path)) {
            return new Failure("has not been loaded. Run bin/console ilios:maintenance:update-frontend");
        }

        return new Success('has been loaded');
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'Ilios Frontend';
    }
}
