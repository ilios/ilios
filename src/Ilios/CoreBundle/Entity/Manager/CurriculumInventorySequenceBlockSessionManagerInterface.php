<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventorySequenceBlockSessionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventorySequenceBlockSessionInterface[]
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
    );

    /**
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function createCurriculumInventorySequenceBlockSession();
}
