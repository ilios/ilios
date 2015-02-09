<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Interface CurriculumInventorySequenceManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventorySequenceManagerInterface
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
     * @return CurriculumInventorySequenceInterface[]|Collection
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
     *
     * @return void
     */
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true
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
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventorySequenceInterface
     */
    public function createCurriculumInventorySequence();
}
