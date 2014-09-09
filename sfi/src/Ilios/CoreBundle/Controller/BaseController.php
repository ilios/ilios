<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

class BaseController extends FOSRestController
{

    public function getUserManager()
    {
        return $this->container->get('ilios_core.manager.user_manager');
    }
}
