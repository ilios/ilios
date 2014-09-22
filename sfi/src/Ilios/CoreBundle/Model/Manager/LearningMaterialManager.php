<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\LearningMaterialManagerInterface;
use Ilios\CoreBundle\Model\LearningMaterialInterface;

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
