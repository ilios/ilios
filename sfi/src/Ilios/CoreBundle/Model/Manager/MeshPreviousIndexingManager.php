<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshPreviousIndexingManagerInterface;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * MeshPreviousIndexingManager
 */
abstract class MeshPreviousIndexingManager implements MeshPreviousIndexingManagerInterface
{
    /**
     * @return MeshPreviousIndexingInterface
     */
     public function createMeshPreviousIndexing()
     {
         $class = $this->getClass();

         return new $class();
     }
}
