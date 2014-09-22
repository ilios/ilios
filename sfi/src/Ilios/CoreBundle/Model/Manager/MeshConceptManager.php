<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshConceptManagerInterface;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * MeshConceptManager
 */
abstract class MeshConceptManager implements MeshConceptManagerInterface
{
    /**
     * @return MeshConceptInterface
     */
     public function createMeshConcept()
     {
         $class = $this->getClass();

         return new $class();
     }
}
