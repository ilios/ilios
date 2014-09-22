<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshDescriptorManagerInterface;
use Ilios\CoreBundle\Model\MeshDescriptorInterface;

/**
 * MeshDescriptorManager
 */
abstract class MeshDescriptorManager implements MeshDescriptorManagerInterface
{
    /**
    * @return MeshDescriptorInterface
    */
    public function createMeshDescriptor()
    {
        $class = $this->getClass();

        return new $class();
    }
}
