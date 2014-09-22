<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshSemanticTypeManagerInterface;
use Ilios\CoreBundle\Model\MeshSemanticTypeInterface;

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
