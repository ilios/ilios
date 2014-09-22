<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\LearningMaterialUserRoleManagerInterface;
use Ilios\CoreBundle\Model\LearningMaterialUserRoleInterface;

/**
 * LearningMaterialUserRoleManager
 */
abstract class LearningMaterialUserRoleManager implements LearningMaterialUserRoleManagerInterface
{
    /**
    * @return LearningMaterialUserRoleInterface
    */
    public function createLearningMaterialUserRole()
    {
        $class = $this->getClass();

        return new $class();
    }
}
