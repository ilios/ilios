<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Result\ResultInterface;
use App\Command\UpdateFrontendCommand;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;

class Frontend implements CheckInterface
{
    public function __construct(private string $kernelProjectDir)
    {
    }

    public function check(): ResultInterface
    {
        $path = UpdateFrontendCommand::getActiveFrontendIndexPath($this->kernelProjectDir);
        if (!file_exists($path)) {
            return new Failure("has not been loaded. Run bin/console ilios:maintenance:update-frontend");
        }

        return new Success('has been loaded');
    }

    public function getLabel(): string
    {
        return 'Ilios Frontend';
    }
}
