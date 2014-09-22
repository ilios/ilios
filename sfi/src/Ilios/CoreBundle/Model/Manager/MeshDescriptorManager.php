<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManagerInterface;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

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
