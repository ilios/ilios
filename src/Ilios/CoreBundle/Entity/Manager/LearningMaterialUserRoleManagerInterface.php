<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Interface LearningMaterialUserRoleManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface LearningMaterialUserRoleManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialUserRoleInterface
     */
    public function findLearningMaterialUserRoleBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LearningMaterialUserRoleInterface[]
     */
    public function findLearningMaterialUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     *
     * @return void
     */
    public function deleteLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole
    );

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function createLearningMaterialUserRole();
}
