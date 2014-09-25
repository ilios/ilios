<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\LearningMaterialUserRoleInterface;

/**
 * Interface LearningMaterialUserRoleManagerInterface
 */
interface LearningMaterialUserRoleManagerInterface
{
    /** 
     *@return LearningMaterialUserRoleInterface
     */
    public function createLearningMaterialUserRole();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialUserRoleInterface
     */
    public function findLearningMaterialUserRoleBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialUserRoleInterface[]|Collection
     */
    public function findLearningMaterialUserRolesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterialUserRole(LearningMaterialUserRoleInterface $learningMaterialUserRole, $andFlush = true);

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     *
     * @return void
     */
    public function deleteLearningMaterialUserRole(LearningMaterialUserRoleInterface $learningMaterialUserRole);

    /**
     * @return string
     */
    public function getClass();
}
