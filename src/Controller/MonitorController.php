<?php

declare(strict_types=1);

namespace App\Controller;

use App\Monitor\Composer;
use App\Monitor\DatabaseConnection;
use App\Monitor\DeprecatedConfigurationOption;
use App\Monitor\Frontend;
use App\Monitor\IliosFileSystem;
use App\Monitor\Migrations;
use App\Monitor\NoDefaultSecret;
use App\Monitor\PhpConfiguration;
use App\Monitor\PhpExtension;
use App\Monitor\RequiredENV;
use App\Monitor\SecretLength;
use App\Monitor\Timezone;
use Laminas\Diagnostics\Check\ApcFragmentation;
use Laminas\Diagnostics\Check\ApcMemory;
use Laminas\Diagnostics\Check\DirReadable;
use Laminas\Diagnostics\Check\DirWritable;
use Laminas\Diagnostics\Check\PhpVersion;
use Laminas\Diagnostics\Result\AbstractResult;
use Laminas\Diagnostics\Result\Collection;
use Laminas\Diagnostics\Runner\Runner;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MonitorController extends AbstractController
{
    #[Route('ilios/health')]
    public function health(
        ApcFragmentation $apcFragmentationCheck,
        ApcMemory $apcMemoryCheck,
        Composer $composerCheck,
        DatabaseConnection $databaseConnectionCheck,
        DeprecatedConfigurationOption $deprecatedConfigurationOptionCheck,
        DirReadable $dirReadableCheck,
        DirWritable $dirWritableCheck,
        Frontend $frontendCheck,
        IliosFileSystem $fileSystemCheck,
        NoDefaultSecret $noDefaultSecretCheck,
        Migrations $migrationsCheck,
        PhpConfiguration $phpConfigCheck,
        PhpExtension $phpExtensionCheck,
        PhpVersion $phpVersionCheck,
        RequiredENV $requiredEnvCheck,
        SecretLength $secretLengthCheck,
        Timezone $timezoneCheck
    ): Response {
        $checks = [
            $apcMemoryCheck,
            $apcFragmentationCheck,
            $composerCheck,
            $databaseConnectionCheck,
            $deprecatedConfigurationOptionCheck,
            $dirReadableCheck,
            $dirWritableCheck,
            $fileSystemCheck,
            $frontendCheck,
            $migrationsCheck,
            $phpConfigCheck,
            $phpExtensionCheck,
            $phpVersionCheck,
            $noDefaultSecretCheck,
            $requiredEnvCheck,
            $secretLengthCheck,
            $timezoneCheck,
        ];
        $results = $this->runChecks($checks);
        $data = $this->processResults($checks, $results);
        return new JsonResponse($data);
    }

    #[Route('ilios/health/minimal')]
    public function minimalHealth(
        DeprecatedConfigurationOption $deprecatedConfigurationOptionCheck,
        DirReadable $dirReadableCheck,
        DirWritable $dirWritableCheck,
        NoDefaultSecret $noDefaultSecretCheck,
        PhpExtension $phpExtensionCheck,
        PhpVersion $phpVersionCheck,
        RequiredENV $requiredEnvCheck,
        SecretLength $secretLengthCheck,
        Timezone $timezoneCheck
    ): Response {
        $checks = [
            $deprecatedConfigurationOptionCheck,
            $dirReadableCheck,
            $dirWritableCheck,
            $phpExtensionCheck,
            $phpVersionCheck,
            $noDefaultSecretCheck,
            $requiredEnvCheck,
            $secretLengthCheck,
            $timezoneCheck,
        ];
        $results = $this->runChecks($checks);
        $data = $this->processResults($checks, $results);
        return new JsonResponse($data);
    }

    protected function processResults(array $checks, Collection $results): array
    {
        $rhett = [];
        foreach ($checks as $check) {
            $result = $results[$check];
            $rhett[] = [
                'check' => $check::class,
                'status' => $this->getStatus($result),
                'message' => $result->getMessage(),
            ];
        }
        $rhett['summary_status'] = $results->getFailureCount() ? 'KO' : 'OK';
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

    protected function runChecks(array $checks): Collection
    {
        $runner = new Runner();
        $runner->addChecks($checks);
        return $runner->run();
    }
}
