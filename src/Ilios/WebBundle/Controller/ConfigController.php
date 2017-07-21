<?php

namespace Ilios\WebBundle\Controller;

use Ilios\AuthenticationBundle\Service\AuthenticationInterface;
use Ilios\CoreBundle\Service\ApplicationConfiguration;
use Ilios\WebBundle\Service\WebIndexFromJson;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    public function indexAction(
        Request $request,
        ApplicationConfiguration $applicationConfiguration,
        AuthenticationInterface $authenticationSystem
    ) {
        $configuration = $authenticationSystem->getPublicConfigurationInformation($request);
        $configuration['locale'] = $this->container->getParameter('locale');

        $ldapUrl = $applicationConfiguration->get('ldap_directory_url');
        if (!empty($ldapUrl)) {
            $configuration['userSearchType'] = 'ldap';
        } else {
            $configuration['userSearchType'] = 'local';
        }
        $configuration['maxUploadSize'] = UploadedFile::getMaxFilesize();
        $configuration['apiVersion'] = WebIndexFromJson::API_VERSION;

        $configuration['trackingEnabled'] = $applicationConfiguration->get('enable_tracking');
        if ($configuration['trackingEnabled']) {
            $configuration['trackingCode'] = $applicationConfiguration->get('tracking_code');
        }

        return new JsonResponse(array('config' => $configuration));
    }
}
