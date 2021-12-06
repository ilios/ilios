<?php

declare(strict_types=1);

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class Timezone implements CheckInterface
{
    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check(): ResultInterface
    {
        $tz = date_default_timezone_get();

        if ($tz === 'UTC') {
            return new Success('Default timezone is UTC');
        }

        return new Failure('Default timezone is not UTC! It is actually ' . $tz);
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Default Timezone';
    }
}
