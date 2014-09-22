<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Model\PermissionInterface;

/**
 * PermissionManager
 */
abstract class PermissionManager implements PermissionManagerInterface
{
    /**
    * @return PermissionInterface
    */
    public function createPermission()
    {
        $class = $this->getClass();

        return new $class();
    }
}
