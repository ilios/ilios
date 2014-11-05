<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventoryInstitutionInterface;

/**
 * Interface CurriculumInventoryInstitutionManagerInterface
 */
interface CurriculumInventoryInstitutionManagerInterface
{
    /** 
     *@return CurriculumInventoryInstitutionInterface
     */
    public function createCurriculumInventoryInstitution();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryInstitutionInterface
     */
    public function findCurriculumInventoryInstitutionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CurriculumInventoryInstitutionInterface[]|Collection
     */
    public function findCurriculumInventoryInstitutionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryInstitution(CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution, $andFlush = true);

    /**
     * @param CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
     *
     * @return void
     */
    public function deleteCurriculumInventoryInstitution(CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution);

    /**
     * @return string
     */
    public function getClass();
}
