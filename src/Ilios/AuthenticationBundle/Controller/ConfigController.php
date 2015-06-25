<?php

namespace Ilios\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConfigController extends Controller
{
    public function indexAction()
    {
        $configuration = [];
        $configuration['type'] = $this->container->getParameter('ilios_authentication.type');
        return new JsonResponse(array('config' => $configuration));
    }
}
