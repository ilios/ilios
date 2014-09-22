<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshTreeXDescriptorManagerInterface;
use Ilios\CoreBundle\Model\MeshTreeXDescriptorInterface;

/**
 * MeshTreeXDescriptorManager
 */
abstract class MeshTreeXDescriptorManager implements MeshTreeXDescriptorManagerInterface
{
    /**
    * @return MeshTreeXDescriptorInterface
    */
    public function createMeshTreeXDescriptor()
    {
        $class = $this->getClass();

        return new $class();
    }
}
