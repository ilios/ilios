<?php

declare(strict_types=1);

namespace App\Service;

use Laminas\Diagnostics\Result\AbstractResult;
use Laminas\Diagnostics\Result\Collection;
use Laminas\Diagnostics\Runner\Runner;
use ReflectionClass;

class HealthCheckRunner
{
    public const string STATUS_OK = 'OK';
    public const string STATUS_NOT_OK = 'KO';

    public function run(array $checks): array
    {
        $runner = new Runner();
        $runner->addChecks($checks);
        return $this->processResults($checks, $runner->run());
    }

    protected function processResults(array $checks, Collection $results): array
    {
        $rhett = ['results' => []];
        foreach ($checks as $check) {
            $result = $results[$check];
            $rhett['results'][] = [
                'check' => $check::class,
                'status' => $this->getStatus($result),
                'message' => $result->getMessage(),
            ];
        }
        $rhett['summary_status'] = $results->getFailureCount() ? self::STATUS_NOT_OK : self::STATUS_OK;
        return $rhett;
    }

    protected function getStatus(AbstractResult $result): string
    {
        // Use the class name of the actual result object as its own label.
        // The four possible values are 'Success', 'Warning', 'Failure', and 'Skip'.
        // Solution for stripping a FQN down to the basename taken from here:
        // https://stackoverflow.com/a/25472778/307333
        return new ReflectionClass($result)->getShortName();
    }
}
