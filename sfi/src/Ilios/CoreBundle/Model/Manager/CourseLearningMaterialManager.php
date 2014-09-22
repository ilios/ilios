<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\CourseLearningMaterialManagerInterface;
use Ilios\CoreBundle\Model\CourseLearningMaterialInterface;

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
