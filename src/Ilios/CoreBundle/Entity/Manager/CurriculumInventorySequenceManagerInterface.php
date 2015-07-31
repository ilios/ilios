<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Interface CurriculumInventorySequenceManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventorySequenceManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceInterface
     */
    public function findCurriculumInventorySequenceBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventorySequenceInterface[]
     */
    public function findCurriculumInventorySequencesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     *
     * @return void
     */
    public function deleteCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence
    );

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function createCurriculumInventorySequence();
}
