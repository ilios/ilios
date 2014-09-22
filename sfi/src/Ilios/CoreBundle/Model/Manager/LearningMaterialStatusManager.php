<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialStatusManagerInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * LearningMaterialStatusManager
 */
abstract class LearningMaterialStatusManager implements LearningMaterialStatusManagerInterface
{
    /**
     * @return LearningMaterialStatusInterface
     */
     public function createLearningMaterialStatus()
     {
         $class = $this->getClass();

         return new $class();
     }
}
