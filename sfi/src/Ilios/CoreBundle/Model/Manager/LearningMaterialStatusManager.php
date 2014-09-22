<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\LearningMaterialStatusManagerInterface;
use Ilios\CoreBundle\Model\LearningMaterialStatusInterface;

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
