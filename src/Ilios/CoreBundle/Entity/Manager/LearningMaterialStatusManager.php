<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialStatusManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findLearningMaterialStatusBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findLearningMaterialStatusesBy(
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
    public function updateLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($learningMaterialStatus, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus
    ) {
        $this->delete($learningMaterialStatus);
    }

    /**
     * @deprecated
     */
    public function createLearningMaterialStatus()
    {
        return $this->create();
    }
}
