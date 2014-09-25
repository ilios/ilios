<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionManagerInterface
 */
interface CurriculumInventorySequenceBlockSessionManagerInterface
{
    /** 
     *@return CurriculumInventorySequenceBlockSessionInterface
     */
    public function createCurriculumInventorySequenceBlockSession();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventorySequenceBlockSessionInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, $andFlush = true);

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession);

    /**
     * @return string
     */
    public function getClass();
}
