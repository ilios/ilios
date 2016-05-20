<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Class ProgramManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findProgramBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findProgramDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);

    }

    /**
     * @deprecated
     */
    public function findProgramsBy(
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
    public function findProgramDTOsBy(
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
    public function updateProgram(
        ProgramInterface $program,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($program, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteProgram(
        ProgramInterface $program
    ) {
        $this->delete($program);
    }

    /**
     * @deprecated
     */
    public function createProgram()
    {
        return $this->create();
    }
}
