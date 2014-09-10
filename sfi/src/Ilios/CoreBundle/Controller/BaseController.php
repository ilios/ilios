<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

use Ilios\CoreBundle\Model\Manager\UserManagerInterface;

/**
 * Class BaseController
 * @package Ilios\CoreBundle\Controller
 * @author Victor Passapera <vpassapera@gmail.com>
 */
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
