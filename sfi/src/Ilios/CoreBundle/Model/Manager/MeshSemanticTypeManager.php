<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshSemanticTypeManagerInterface;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * MeshSemanticTypeManager
 */
abstract class MeshSemanticTypeManager implements MeshSemanticTypeManagerInterface
{
    /**
     * @return MeshSemanticTypeInterface
     */
     public function createMeshSemanticType()
     {
         $class = $this->getClass();

         return new $class();
     }
}
