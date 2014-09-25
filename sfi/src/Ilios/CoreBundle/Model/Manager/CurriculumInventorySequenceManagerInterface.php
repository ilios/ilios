<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceInterface;

/**
 * Interface CurriculumInventorySequenceManagerInterface
 */
interface CurriculumInventorySequenceManagerInterface
{
    /** 
     *@return CurriculumInventorySequenceInterface
     */
    public function createCurriculumInventorySequence();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function findCurriculumInventorySequenceBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventorySequenceInterface[]|Collection
     */
    public function findCurriculumInventorySequencesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequence(CurriculumInventorySequenceInterface $curriculumInventorySequence, $andFlush = true);

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     *
     * @return void
     */
    public function deleteCurriculumInventorySequence(CurriculumInventorySequenceInterface $curriculumInventorySequence);

    /**
     * @return string
     */
    public function getClass();
}
