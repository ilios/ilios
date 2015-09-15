<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Interface CurriculumInventoryAcademicLevelManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventoryAcademicLevelManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function findCurriculumInventoryAcademicLevelBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
     */
    public function findCurriculumInventoryAcademicLevelsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return void
     */
    public function deleteCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
    );

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function createCurriculumInventoryAcademicLevel();
}
