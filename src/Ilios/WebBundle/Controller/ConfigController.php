<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ConfigController
 * @package Ilios\WebBundle\Controller
 */
class ConfigController extends Controller
{
    public function indexAction()
    {
        $configuration = [];
        $type = $this->container->getParameter('ilios_authentication.type');
        $configuration['type'] = $type;
        if ($type == 'shibboleth') {
            $url = $this->get('request')->getSchemeAndHttpHost();
            $configuration['loginUrl'] = $url . '/Shibboleth.sso/Login';
        }
        return new JsonResponse(array('config' => $configuration));
    }
}
