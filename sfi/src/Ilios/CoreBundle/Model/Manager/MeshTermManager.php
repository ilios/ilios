<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshTermManagerInterface;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTermManager
 */
abstract class MeshTermManager implements MeshTermManagerInterface
{
    /**
     * @return MeshTermInterface
     */
     public function createMeshTerm()
     {
         $class = $this->getClass();

         return new $class();
     }
}
