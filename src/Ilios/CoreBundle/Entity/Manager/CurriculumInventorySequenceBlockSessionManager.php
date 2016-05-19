<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceBlockSessionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCurriculumInventorySequenceBlockSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(
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
    public function updateCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($curriculumInventorySequenceBlockSession, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
    ) {
        $this->delete($curriculumInventorySequenceBlockSession);
    }

    /**
     * @deprecated
     */
    public function createCurriculumInventorySequenceBlockSession()
    {
        return $this->create();
    }
}
