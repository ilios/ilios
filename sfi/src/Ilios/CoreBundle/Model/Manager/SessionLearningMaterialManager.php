<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * SessionLearningMaterialManager
 */
abstract class SessionLearningMaterialManager implements SessionLearningMaterialManagerInterface
{
    /**
     * @return SessionLearningMaterialInterface
     */
     public function createSessionLearningMaterial()
     {
         $class = $this->getClass();

         return new $class();
     }
}
