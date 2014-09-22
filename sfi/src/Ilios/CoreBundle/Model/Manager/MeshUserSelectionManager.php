<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshUserSelectionManagerInterface;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * MeshUserSelectionManager
 */
abstract class MeshUserSelectionManager implements MeshUserSelectionManagerInterface
{
    /**
     * @return MeshUserSelectionInterface
     */
     public function createMeshUserSelection()
     {
         $class = $this->getClass();

         return new $class();
     }
}
