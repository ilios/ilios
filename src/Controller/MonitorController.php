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
use App\Service\HealthCheckRunner;
use Laminas\Diagnostics\Check\ApcFragmentation;
use Laminas\Diagnostics\Check\ApcMemory;
use Laminas\Diagnostics\Check\DirReadable;
use Laminas\Diagnostics\Check\DirWritable;
use Laminas\Diagnostics\Check\PhpVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MonitorController extends AbstractController
{
    #[Route('ilios/health')]
    public function health(
        HealthCheckRunner $runner,
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
        $rhett = $runner->run([
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
        ]);
        return new JsonResponse($rhett);
    }

    #[Route('ilios/health/minimal')]
    public function minimalHealth(
        HealthCheckRunner $runner,
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
        $rhett = $runner->run([
            $deprecatedConfigurationOptionCheck,
            $dirReadableCheck,
            $dirWritableCheck,
            $phpExtensionCheck,
            $phpVersionCheck,
            $noDefaultSecretCheck,
            $requiredEnvCheck,
            $secretLengthCheck,
            $timezoneCheck,
        ]);
        return new JsonResponse($rhett);
    }
}
