<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Class CurriculumInventoryAcademicLevelManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryAcademicLevelManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCurriculumInventoryAcademicLevelBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCurriculumInventoryAcademicLevelsBy(
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
    public function updateCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($curriculumInventoryAcademicLevel, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
    ) {
        $this->delete($curriculumInventoryAcademicLevel);
    }

    /**
     * @deprecated
     */
    public function createCurriculumInventoryAcademicLevel()
    {
        return $this->create();
    }
}
