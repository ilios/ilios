<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

use Ilios\CoreBundle\Model\Manager\UserManagerInterface;

class BaseController extends FOSRestController
{
    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->container->get('ilios_core.manager.user_manager');
    }
}
