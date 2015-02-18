<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Interface CurriculumInventoryInstitutionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventoryInstitutionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryInstitutionInterface
     */
    public function findCurriculumInventoryInstitutionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventoryInstitutionInterface[]|Collection
     */
    public function findCurriculumInventoryInstitutionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution,
        $andFlush = true
    );

    /**
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     *
     * @return void
     */
    public function deleteCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventoryInstitutionInterface
     */
    public function createCurriculumInventoryInstitution();
}
