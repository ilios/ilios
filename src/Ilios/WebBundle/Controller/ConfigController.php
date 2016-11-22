<?php

namespace Ilios\WebBundle\Controller;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ConfigController
 * @package Ilios\WebBundle\Controller
 */
class ConfigController extends Controller
{
    public function indexAction(Request $request)
    {
        $configuration = [];
        $authenticationType = $this->container->getParameter('ilios_authentication.type');

        $configuration['type'] = $authenticationType;
        if ($authenticationType == 'shibboleth') {
            $loginPath = $this->container->getParameter('ilios_authentication.shibboleth.login_path');
            $url = $request->getSchemeAndHttpHost();
            $configuration['loginUrl'] = $url . $loginPath;
        }
        if ($authenticationType === 'cas') {
            $cas = $this->container->get('ilios_authentication.cas.manager');

            $configuration['casLoginUrl'] = $cas->getLoginUrl();
        }
        $configuration['locale'] = $this->container->getParameter('locale');

        $ldapUrl = $this->container->getParameter('ilios_core.ldap.url');
        if (!empty($ldapUrl)) {
            $configuration['userSearchType'] = 'ldap';
        } else {
            $configuration['userSearchType'] = 'local';
        }
        $configuration['maxUploadSize'] = UploadedFile::getMaxFilesize();
        $configuration['apiVersion'] = WebIndexFromJson::API_VERSION;

        $configuration['trackingEnabled'] = $this->container->getParameter('ilios_core.enable_tracking');
        if ($configuration['trackingEnabled']) {
            $configuration['trackingCode'] = $this->container->getParameter('ilios_core.tracking_code');
        }

        return new JsonResponse(array('config' => $configuration));
    }
}
