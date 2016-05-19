<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCurriculumInventorySequenceBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCurriculumInventorySequencesBy(
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
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($curriculumInventorySequence, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence
    ) {
        $this->delete($curriculumInventorySequence);
    }

    /**
     * @deprecated
     */
    public function createCurriculumInventorySequence()
    {
        return $this->create();
    }
}
