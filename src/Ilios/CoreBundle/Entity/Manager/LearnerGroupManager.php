<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearnerGroupManager extends BaseManager
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
     * @param array $criteria
     * @param array|null $orderBy
     * @return LearnerGroupDTO|bool
     */
    public function findLearnerGroupDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
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
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return LearnerGroupDTO[]
     */
    public function findLearnerGroupDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
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
