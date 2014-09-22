<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshConceptXTermManagerInterface;
use Ilios\CoreBundle\Entity\MeshConceptXTermInterface;

/**
 * MeshConceptXTermManager
 */
abstract class MeshConceptXTermManager implements MeshConceptXTermManagerInterface
{
    /**
     * @return MeshConceptXTermInterface
     */
     public function createMeshConceptXTerm()
     {
         $class = $this->getClass();

         return new $class();
     }
}
