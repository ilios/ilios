<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshPreviousIndexingManagerInterface;
use Ilios\CoreBundle\Model\MeshPreviousIndexingInterface;

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
