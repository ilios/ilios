<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialUserRoleManagerInterface;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

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
