<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshTermManagerInterface;
use Ilios\CoreBundle\Model\MeshTermInterface;

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
