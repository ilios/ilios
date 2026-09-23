<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Override;

class PhpExtension implements CheckInterface
{
    public function __construct(protected array $extensions)
    {
    }

    #[Override]
    public function check(): ResultInterface
    {
        $missingExtensions = [];
        foreach ($this->extensions as $extension) {
            if (! extension_loaded($extension)) {
                $missingExtensions[] = $extension;
            }
        }

        if (empty($missingExtensions)) {
            return new Success(implode(', ', $this->extensions) . ' PHP extensions loaded.');
        }

        return new Failure(implode(', ', $missingExtensions) . ' PHP extensions not loaded.');
    }

    #[Override]
    public function getLabel(): string
    {
        return 'PHP extensions';
    }
}
