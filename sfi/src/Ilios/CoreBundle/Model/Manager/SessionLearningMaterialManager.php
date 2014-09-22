<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\SessionLearningMaterialManagerInterface;
use Ilios\CoreBundle\Model\SessionLearningMaterialInterface;

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
