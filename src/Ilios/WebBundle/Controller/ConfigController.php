<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use phpCAS;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ConfigController
 * @package Ilios\WebBundle\Controller
 */
class ConfigController extends Controller
{
    public function indexAction()
    {
        $configuration = [];
        $authenticationType = $this->container->getParameter('ilios_authentication.type');

        $configuration['type'] = $authenticationType;
        if ($authenticationType == 'shibboleth') {
            $loginPath = $this->container->getParameter('ilios_authentication.shibboleth.login_path');
            $url = $this->get('request')->getSchemeAndHttpHost();
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

        return new JsonResponse(array('config' => $configuration));
    }
}
