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
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{
    #[Route('ilios/health-check')]
    public function health(
        Request $request,
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
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if ($acceptHeader->has('text/html')) {
            return $this->render('health-check/index.twig', [ 'data' => $rhett ]);
        }
        return new JsonResponse($rhett);
    }

    #[Route('ilios/health-check/minimal')]
    public function minimalHealth(
        Request $request,
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
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if ($acceptHeader->has('text/html')) {
            return $this->render('health-check/index.twig', [ 'data' => $rhett ]);
        }
        return new JsonResponse($rhett);
    }
}
