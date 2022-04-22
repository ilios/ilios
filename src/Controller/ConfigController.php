<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthenticationInterface;
use App\Service\Config;
use App\Service\Index\Curriculum;
use Shivas\VersioningBundle\Service\VersionManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    #[Route(
        '/application/config',
        methods: ['GET'],
    )]
    public function getConfig(
        Request $request,
        Config $config,
        Curriculum $curriculumSearch,
        AuthenticationInterface $authenticationSystem,
        VersionManagerInterface $versionManager,
    ): JsonResponse {
        $configuration = $authenticationSystem->getPublicConfigurationInformation($request);
        $configuration['locale'] = $this->getParameter('kernel.default_locale');

        $ldapUrl = $config->get('ldap_directory_url');
        if (!empty($ldapUrl)) {
            $configuration['userSearchType'] = 'ldap';
        } else {
            $configuration['userSearchType'] = 'local';
        }
        $configuration['maxUploadSize'] = UploadedFile::getMaxFilesize();
        $configuration['apiVersion'] = $this->getParameter('ilios_api_version');
        $configuration['appVersion'] = $versionManager->getVersion();

        $configuration['trackingEnabled'] = $config->get('enable_tracking');
        if ($configuration['trackingEnabled']) {
            $configuration['trackingCode'] = $config->get('tracking_code');
        }
        $configuration['searchEnabled'] = $curriculumSearch->isEnabled();

        $configuration['academicYearCrossesCalendarYearBoundaries']
            = $config->get('academic_year_crosses_calendar_year_boundaries');

        return new JsonResponse(['config' => $configuration]);
    }
}
