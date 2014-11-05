<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevelInterface;

/**
 * Interface CurriculumInventoryAcademicLevelManagerInterface
 */
interface CurriculumInventoryAcademicLevelManagerInterface
{
    /** 
     *@return CurriculumInventoryAcademicLevelInterface
     */
    public function createCurriculumInventoryAcademicLevel();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function findCurriculumInventoryAcademicLevelBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CurriculumInventoryAcademicLevelInterface[]|Collection
     */
    public function findCurriculumInventoryAcademicLevelsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryAcademicLevel(CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel, $andFlush = true);

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return void
     */
    public function deleteCurriculumInventoryAcademicLevel(CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel);

    /**
     * @return string
     */
    public function getClass();
}
