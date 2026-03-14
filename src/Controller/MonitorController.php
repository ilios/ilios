<?php

declare(strict_types=1);

namespace App\Controller;

use App\Monitor\Composer;
use App\Monitor\DatabaseConnection;
use App\Monitor\DeprecatedConfigurationOption;
use App\Monitor\Frontend;
use App\Monitor\IliosFileSystem;
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
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Laminas\Diagnostics\Runner\Runner;
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
            $phpConfigCheck,
            $phpExtensionCheck,
            $phpVersionCheck,
            $noDefaultSecretCheck,
            $requiredEnvCheck,
            $secretLengthCheck,
            $timezoneCheck,
        ];
        $results = $this->runChecks($checks);

        $rhett = [];
        foreach ($checks as $check) {
            $result = $results[$check];
            $rhett[] = [
                'check' => $check::class,
                'description' => $check->getLabel(),
                'status' => $this->getStatusText($result),
                'message' => $result->getMessage(),
            ];
        }
        $rhett['summary_status'] = $results->getFailureCount() ? 'KO' : 'OK';
        return new JsonResponse(
            $rhett
        );
    }
    protected function getStatusText(AbstractResult $result): string
    {
        return match (get_class($result)) {
            Success::class => 'Success',
            Failure::class => 'Failure',
            Warning::class => 'Warning',
            Skip::class => 'Skip',
        };
    }

    protected function runChecks(array $checks): Collection
    {
        $runner = new Runner();
        $runner->addChecks($checks);
        return $runner->run();
    }
}
