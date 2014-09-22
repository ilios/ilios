<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshUserSelectionManagerInterface;
use Ilios\CoreBundle\Model\MeshUserSelectionInterface;

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
