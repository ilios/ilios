<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshConceptManagerInterface;
use Ilios\CoreBundle\Model\MeshConceptInterface;

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
