<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialUserRoleManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findLearningMaterialUserRoleBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findLearningMaterialUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($learningMaterialUserRole, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole
    ) {
        $this->delete($learningMaterialUserRole);
    }

    /**
     * @deprecated
     */
    public function createLearningMaterialUserRole()
    {
        return $this->create();
    }
}
