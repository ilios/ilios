<?php

namespace App\Controller;

use App\Service\AuthenticationInterface;
use App\Service\Config;
use App\Service\Search;
use Jean85\PrettyVersions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ConfigController
 */
class ConfigController extends AbstractController
{
    public function indexAction(
        Request $request,
        Config $config,
        Search $search,
        AuthenticationInterface $authenticationSystem
    ) {
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
        $iliosVersion = PrettyVersions::getVersion('ilios/ilios');
        $configuration['iliosVersion'] = 'v' . $iliosVersion->getPrettyVersion();

        $configuration['trackingEnabled'] = $config->get('enable_tracking');
        if ($configuration['trackingEnabled']) {
            $configuration['trackingCode'] = $config->get('tracking_code');
        }
        $configuration['searchEnabled'] = $search->isEnabled();

        return new JsonResponse(array('config' => $configuration));
    }
}
