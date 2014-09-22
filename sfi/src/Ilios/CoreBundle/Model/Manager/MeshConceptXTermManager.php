<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshConceptXTermManagerInterface;
use Ilios\CoreBundle\Model\MeshConceptXTermInterface;

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
