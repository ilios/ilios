<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\MeshQualifierManagerInterface;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

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
