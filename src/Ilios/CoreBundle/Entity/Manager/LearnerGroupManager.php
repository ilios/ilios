<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearnerGroupManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findLearnerGroupBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findLearnerGroupDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findLearnerGroupsBy(
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
    public function findLearnerGroupDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateLearnerGroup(
        LearnerGroupInterface $learnerGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($learnerGroup, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteLearnerGroup(
        LearnerGroupInterface $learnerGroup
    ) {
        $this->delete($learnerGroup);
    }

    /**
     * @deprecated
     */
    public function createLearnerGroup()
    {
        return $this->create();
    }
}
