<?php

namespace App\Monitor;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class Timezone implements CheckInterface
{
    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
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
    public function getLabel()
    {
        return 'Default Timezone';
    }
}
