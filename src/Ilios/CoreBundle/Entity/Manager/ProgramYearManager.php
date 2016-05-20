<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findProgramYearBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }
    
    /**
     * @deprecated
     */
    public function findProgramYearDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findProgramYearsBy(
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
    public function findProgramYearDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateProgramYear(
        ProgramYearInterface $programYear,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($programYear, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteProgramYear(
        ProgramYearInterface $programYear
    ) {
        $this->delete($programYear);
    }

    /**
     * @deprecated
     */
    public function createProgramYear()
    {
        return $this->create();
    }
}
