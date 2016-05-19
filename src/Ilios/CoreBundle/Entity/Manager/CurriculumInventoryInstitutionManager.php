<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Class CurriculumInventoryInstitutionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryInstitutionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCurriculumInventoryInstitutionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCurriculumInventoryInstitutionsBy(
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
    public function updateCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($curriculumInventoryInstitution, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCurriculumInventoryInstitution(
        CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ) {
        $this->delete($curriculumInventoryInstitution);
    }

    /**
     * @deprecated
     */
    public function createCurriculumInventoryInstitution()
    {
        return $this->create();
    }
}
