<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManagerInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * CourseLearningMaterialManager
 */
abstract class CourseLearningMaterialManager implements CourseLearningMaterialManagerInterface
{
    /**
     * @return CourseLearningMaterialInterface
     */
     public function createCourseLearningMaterial()
     {
         $class = $this->getClass();

         return new $class();
     }
}
