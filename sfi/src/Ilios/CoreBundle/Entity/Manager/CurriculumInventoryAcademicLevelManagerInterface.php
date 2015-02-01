<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Interface CurriculumInventoryAcademicLevelManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventoryAcademicLevelManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function createCurriculumInventoryAcademicLevel();
}