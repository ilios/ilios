<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Interface CurriculumInventoryInstitutionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventoryInstitutionManagerInterface extends ManagerInterface
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
     * @return ArrayCollection|CurriculumInventoryInstitutionInterface[]
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
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution,
        $andFlush = true,
        $forceId = false
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
     * @return CurriculumInventoryInstitutionInterface
     */
    public function createCurriculumInventoryInstitution();
}
