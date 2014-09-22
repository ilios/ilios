<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshTreeXDescriptorManagerInterface;
use Ilios\CoreBundle\Entity\MeshTreeXDescriptorInterface;

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
