<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\MeshQualifierManagerInterface;
use Ilios\CoreBundle\Model\MeshQualifierInterface;

/**
 * MeshQualifierManager
 */
abstract class MeshQualifierManager implements MeshQualifierManagerInterface
{
    /**
    * @return MeshQualifierInterface
    */
    public function createMeshQualifier()
    {
        $class = $this->getClass();

        return new $class();
    }
}
