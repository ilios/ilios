<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * LearningMaterialManager
 */
abstract class LearningMaterialManager implements LearningMaterialManagerInterface
{
    /**
     * @return LearningMaterialInterface
     */
     public function createLearningMaterial()
     {
         $class = $this->getClass();

         return new $class();
     }
}
